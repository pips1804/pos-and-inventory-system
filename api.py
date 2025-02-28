import requests

response = requests.get("http://192.168.100.11:5000/api/products")

if response.status_code == 200:
    products = response.json()
    print(products)  # Process the data in POS system
else:
    print("Error fetching products:", response.status_code)

