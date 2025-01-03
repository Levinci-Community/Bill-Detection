import os
os.environ["KMP_DUPLICATE_LIB_OK"]="TRUE"
import base64
import sys
import codecs
sys.stdout.reconfigure(encoding='utf-8')
import fileinput
import easyocr
import json
from pathlib import Path

import numpy as np
import cv2
import math
import imutils
from screeninfo import get_monitors
from difflib import SequenceMatcher, get_close_matches


filename = sys.argv[1]
# filename = "uid01"

def resize_to_screen(image, max_width, max_height):
    """
    Resize the image to fit within the screen resolution while maintaining the aspect ratio.
    """
    h, w = image.shape[:2]
    scale = min(max_width / w, max_height / h)
    new_w, new_h = int(w * scale), int(h * scale)
    resized_image = cv2.resize(image, (new_w, new_h), interpolation=cv2.INTER_AREA)
    return resized_image

def rotate_bill(img):
    """
    Rotate the bill image to make it upright based on the longest side.
    """
    # Convert to HSV color space for color-based segmentation
    # print("Converting image to HSV color space...")
    hsv = cv2.cvtColor(img, cv2.COLOR_BGR2HSV)
    # Define the range for white color (tuning may be required)
    # print("Creating a mask for white regions...")
    lower_white = np.array([0, 0, 0])  # Lower bound for white in HSV
    upper_white = np.array([95, 5, 100])  # Upper bound for white in HSV
    mask = cv2.inRange(hsv, lower_white, upper_white)
    # Find contours from the mask
    # print("Finding contours...")
    
    
    result = cv2.findContours(mask, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)

    if len(result) == 2:
        contours, hierarchy = result
    elif len(result) == 3:
        _, contours, hierarchy = result
    else:
        raise ValueError("Unexpected number of values returned by cv2.findContours")
    if not contours:
        # print("Error: No white region found!")
        return img  # Return the original image if no contours are found
    # print(f"Found {len(contours)} contours.")
    # Filter contours to find the largest rectangular contour
    # print("Finding the largest contour...")
    largest_contour = max(contours, key=cv2.contourArea)
    # print("Largest contour found.")

    # Calculate the minimum area rectangle for the largest contour
    rect = cv2.minAreaRect(largest_contour)
    box = cv2.boxPoints(rect)
    box = np.int32(box)

    # Identify the two points that represent the longest side
    # print("Identifying the longest side...")
    distances = [
        (np.linalg.norm(box[i] - box[(i + 1) % 4]), box[i], box[(i + 1) % 4])
        for i in range(4)
    ]
    longest_side = max(distances, key=lambda x: x[0])
    point1, point2 = longest_side[1], longest_side[2]
    # print(f"The two points of the longest side are: Point 1: {point1}, Point 2: {point2}")

    # Calculate rotation angle
    # print("Calculating the rotation angle...")
    dx = point2[0] - point1[0]
    dy = -(point2[1] - point1[1])  # Negate for image coordinate system
    alpha = math.degrees(math.atan2(dy, dx))
    rotation = 90 - alpha
    # print(f"Rotation angle calculated: {rotation:.2f} degrees.")
    # Rotate the image
    # print("Rotating the image...")
    rotated_image = imutils.rotate_bound(img, -rotation)
    # print("Image rotated successfully.")
    return rotated_image, box

def detect_bill(rotated_image):
    # Read image and perform OCR

    reader = easyocr.Reader(['vi'], gpu = True)  # Initialize EasyOCR with Vietnamese language
    result = reader.readtext(rotated_image)

    # Extract boxes, texts, and scores
    boxes = [line[0] for line in result]
    texts = [line[1] for line in result]
    scores = [line[2] for line in result]
        
    # Calculate and print average confidence
    if scores:
        average_confidence = sum(scores) / len(scores)
        # print(f"Average Confidence: {average_confidence:.2f}")
    # else:
        # print("No text detected to calculate confidence.")

    # Print detected texts
    # print("Detected Texts:", texts)
    return texts, average_confidence

def key_value(texts):
    # Keys to extract (case-insensitive and space-insensitive)
    # with open("key.txt", "r") as file:
    #     line = file.readline().strip()
    # keys = keys_string.split(",")
    # keys = ["Date", "Trans#", "Sub Total", "Discount", "VAT", "Total", "Voucher Code"]
    key_file = open("C:\\Apache24\\htdocs\\VHost\\hydra-cam0.ddns.net\\API\\bill_capture\\ACCESS_PYTHON_API\\key.txt", "r")
    key_file_contain = key_file.read()
    keys = key_file_contain.split(",")
    # Dictionary to store results
    result = {}

    # Function to normalize strings: remove extra spaces, convert to lowercase, remove special characters like ':'
    def normalize(text):
        return text.strip().lower().replace(" ", "")

    # Function to calculate similarity using SequenceMatcher
    def is_similar(a, b, threshold=0.7):
        return SequenceMatcher(None, a, b).ratio() >= threshold
    def is_present(c, d):
        return get_close_matches(c, [d[i:i+len(c)] for i in range(len(d) - len(c) + 1)], n=1, cutoff=0.7)

    # Extract key-value pairs
    for key in keys:
        normalized_key = normalize(key)  # Normalize the key for comparison
        for i, item in enumerate(texts):  # Iterate through the original list with index
            normalized_item = normalize(item)  # Normalize the current item
            if is_similar(normalized_key, normalized_item):  # Allow approximate matching
                if i + 1 < len(texts):  # Ensure there's a value after the key
                    result[key] = texts[i + 1]
                break  # Exit the loop once the key is found
            elif normalized_key in normalized_item or is_present(normalized_key, normalized_item):
                if ":" in normalized_item:  # If item contains a colon
                    value = item.split(":", 1)[1].strip()  # Extract value after colon
                    if value:  # If value is not empty, use it
                        result[key] = value
                    else:  # If no value after colon, take the next item as the value
                        if i + 1 < len(texts):  # Ensure there's a next item
                            result[key] = texts[i + 1]
    # Output results
    
    if result:
        # print("VALUABLE INFORMATION: ")
        data_dict = dict(result.items())
        # json_data = json.dumps(data_dict, indent=4)
        print(json.dumps(data_dict))
        # print(json_data)
        # for key, value in result.items():
            # print(f"{key}: {value}")
        # json_str = json.dumps(data)
        # print(json_str)
    # else:
    #     data = {}
    #     # print("No matches found.")
    #     data['Response'] = "No matches found."
    #     json_data = json.dumps(data)
        # json_str = json.dumps(data)
        # print(json_str)






f = open("C:\\Apache24\\htdocs\\VHost\\hydra-cam0.ddns.net\\API\\bill_capture\\ACCESS_PYTHON_API\\" + filename+ "\\base64_imgstring.txt", "r")
imgstring = f.read()
f.close()
f = open("C:\\Apache24\\htdocs\\VHost\\hydra-cam0.ddns.net\\API\\bill_capture\\ACCESS_PYTHON_API\\" + filename+ "\\base64_imgstring.txt", "w")
f.truncate()
f.close()
path = "C:\\Apache24\\htdocs\\VHost\\hydra-cam0.ddns.net\\API\\bill_capture\\ACCESS_PYTHON_API\\" + filename+ "\\bill_image.jpg"
imgdata = base64.b64decode(imgstring)
with open(path, 'wb') as f:
   f.write(imgdata)
	
img = cv2.imread(path)
#print(path)
if img is None:
    raise FileNotFoundError("Image not found. Check the file path and try again.")
# Rotate the bill
# print("Rotating bill ... ")
rotated_image, box = rotate_bill(img)
# Get screen resolution
monitor = get_monitors()[0]
screen_width, screen_height = monitor.width, monitor.height
# Draw the rectangle on the original image
# print("Drawing results on the original image...")
img_rectangle = img.copy()
cv2.drawContours(img_rectangle, [box], -1, (0, 255, 0), 2)
# Display bill contour
# print("Displaying contour ...")
# cv2.imshow("Original Image with Rectangle", resize_to_screen(img_rectangle, screen_width, screen_height))
# Extract bill
# print("Extracting bill ... ")
texts, average_confidence = detect_bill(rotated_image)
if average_confidence < 0.5:
    rotated_image_2 = cv2.rotate(rotated_image, cv2.ROTATE_180)
    texts_2, average_confidence_2 = detect_bill(rotated_image_2)
    # print(f"Average Confidence: {average_confidence_2:.2f}")
    key_value(texts_2)
else: 
    # print(f"Average Confidence: {average_confidence:.2f}")
    key_value(texts)



# data_dict = dict(data)
# json_data = json.dumps(data_dict, indent=4)
# print(json_data)
# cv2.waitKey(0)
# cv2.destroyAllWindows()

