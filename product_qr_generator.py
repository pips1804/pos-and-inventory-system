import qrcode
import json
import os

def generate_qr():

    folder_name = "Products_qr_codes"
    if not os.path.exists(folder_name):
        os.makedirs(folder_name)

    category_id = input("Enter category ID: ")
    brand_id = input("Enter brand ID: ")
    product_name = input("Enter product name: ")
    model = input("Enter model[ex:P-1001]: ")
    description = input("Enter description: ")
    quantity = input("Enter quantity: ")
    unit = input("Enter unit[box,bottles,bags]:")
    base_price = input("Enter base price: ")
    tax = input("Enter tax[12]: ")
    min_order = input("Enter minimum order: ")
    supplier = input("Enter supplier: ")
    status = input("Enter status[active,inactive]: ")

    # Store all product details in JSON format
    product_data = {
        "category_id": category_id,
        "brand_id": brand_id,
        "name": product_name,
        "model": model,
        "description": description,
        "quantity": quantity,
        "unit": unit,
        "base_price": base_price,
        "tax": tax,
        "min_order": min_order,
        "supplier": supplier,
        "status": status
    }

    qr_data = json.dumps(product_data)

    # Generate QR code
    qr = qrcode.make(qr_data)

    # Define file path
    file_path = os.path.join(folder_name, f"{product_name}.png")

    # Save QR code in the folder
    qr.save(file_path)

    print(f"QR Code saved at {file_path}")

generate_qr()
