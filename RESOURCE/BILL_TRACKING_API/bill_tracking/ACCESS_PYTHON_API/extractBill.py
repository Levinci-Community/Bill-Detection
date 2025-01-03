import cv2
import easyocr
import json

path = 'bill28.jpg'
img = cv2.imread(path)

reader = easyocr.Reader(['en', 'vi'])

result = reader.readtext(img)

boxes = [line[0] for line in result]
texts = [line[1] for line in result]
scores = [line[2] for line in result]
print(texts)

i = 0
data = {}
for text in texts:
    
    if 'Trans' in text:
        print(texts[i])
        data['Trans#'] = texts[i][9:]

    if 'Serv' in text:
        print(texts[i])
        data['Serv'] = texts[i][5:]

    if 'Date' in text:
        print(texts[i])
        data['Date'] = texts[i][6:]

    if 'Sub Tota' in text:
        print(texts[i] + ' ' + texts[i+1])
        data[texts[i]] = texts[i+1]

    if 'Discount' in text:
        print(texts[i]+ ' '  + texts[i+1])
        data[texts[i]] = texts[i+1]

    if 'VAT' in text and len(text)<=4 :
        print(texts[i]+ ' ' + texts[i+1])
        data[texts[i]] = texts[i+1]

    if 'TOTAL' in text:
        print(texts[i]+ ' ' + texts[i+1])
        data[texts[i]] = texts[i+1]
    
    i = i + 1

json_str = json.dumps(data)
print(json_str)
