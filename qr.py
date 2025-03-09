from flask import Flask, request, jsonify
import qrcode
import os
from flask_cors import CORS
from PIL import Image
import cv2
import numpy as np
import pyzbar.pyzbar as pyzbar
import mysql.connector
import requests

app = Flask(__name__)
CORS(app)

# Folder to store QR codes in the POS system
QR_FOLDER = "QR_CODES"
IMS_API_URL = "http://192.168.1.3:5000/api/update_stock"
os.makedirs(QR_FOLDER, exist_ok=True)

def get_db_connection():
    return mysql.connector.connect(
        host="localhost",      # Change this if needed
        user="root",           # Your MySQL username
        password="",           # Your MySQL password
        database="pos_system"      # Change to your actual database name
    )

@app.route('/generate_qr', methods=['POST'])
def generate_qr():
    try:
        data = request.get_json()
        order_id = data.get("order_id")
        products = data.get("products", [])  # Get product details from request

        if not order_id:
            return jsonify({"status": "error", "message": "Order ID is required"}), 400

        # Convert product details to a string
        product_str = ",".join([f"{p['product_id']}:{p['quantity']}" for p in products])

        # QR Code content now includes order ID + product details
        qr_data = f"http://localhost:5001/confirm_delivery?order_id={order_id}&products={product_str}"
        qr = qrcode.make(qr_data)
        qr_path = os.path.join(QR_FOLDER, f"{order_id}.png")
        qr.save(qr_path)

        return jsonify({"status": "success", "qr_code": qr_path})
    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500


@app.route('/confirm_delivery', methods=['POST'])
def confirm_delivery():
    try:
        if 'qr_code' not in request.files:
            return jsonify({"status": "error", "message": "No QR code file uploaded"}), 400

        file = request.files['qr_code']
        file_path = os.path.join(QR_FOLDER, file.filename)
        file.save(file_path)

        # Decode QR Code
        image = cv2.imread(file_path)
        decoded_objects = pyzbar.decode(image)

        if not decoded_objects:
            return jsonify({"status": "error", "message": "Invalid QR code"}), 400

        qr_text = decoded_objects[0].data.decode("utf-8")

        # Extract order_id and products from the QR data
        if "order_id=" in qr_text and "products=" in qr_text:
            order_id = qr_text.split("order_id=")[-1].split("&")[0]
            products_str = qr_text.split("products=")[-1]
            products = [
                {"product_id": int(p.split(":")[0]), "quantity": int(p.split(":")[1])}
                for p in products_str.split(",")
            ]  # Converts '1:2,2:3' → [{'product_id': 1, 'quantity': 2}, {'product_id': 2, 'quantity': 3}]
        else:
            return jsonify({"status": "error", "message": "Invalid QR code format"}), 400

        # Connect to POS database to update delivery status
        conn = get_db_connection()
        cursor = conn.cursor()

        update_delivery_query = "UPDATE deliver SET delivered = 1 WHERE order_id = %s"
        cursor.execute(update_delivery_query, (order_id,))
        conn.commit()

        cursor.close()
        conn.close()

        # Call IMS API to update stock
        ims_response = requests.post(IMS_API_URL, json={"products": products})
        ims_data = ims_response.json()

        if ims_response.status_code != 200 or ims_data.get("status") != "success":
            return jsonify({"status": "error", "message": "Failed to update stock in IMS"}), 500

        return jsonify({"status": "success", "message": f"Delivery confirmed for Order ID {order_id}. Stock updated."})

    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5001)
