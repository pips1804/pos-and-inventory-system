import requests

response = requests.get("http://192.168.100.30:5000/api/products")

if response.status_code == 200:
    products = response.json()
    print(products)  # Process the data in POS system
else:
    print("Error fetching products:", response.status_code)

# Define the API endpoint for purchases
api_url = "http://192.168.100.30:5000/api/inventory"

# Make a GET request to fetch purchases
response = requests.get(api_url)

if response.status_code == 200:
    purchases = response.json()
    print(purchases)  # Process the data in the POS system
else:
    print("Error fetching purchases:", response.status_code)
