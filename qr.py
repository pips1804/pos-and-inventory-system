from flask import Flask, request, jsonify
import qrcode
import os
from flask_cors import CORS
from PIL import Image
import cv2
import numpy as np
import pyzbar.pyzbar as pyzbar
import mysql.connector

app = Flask(__name__)
CORS(app)

# Folder to store QR codes in the POS system
QR_FOLDER = "QR_CODES"
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

        if not order_id:
            return jsonify({"status": "error", "message": "Order ID is required"}), 400

        # Create QR code content
        qr_data = f"http://localhost:5000/confirm_delivery?order_id={order_id}"
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
        print(f"📸 Received file: {file.filename}")  # Debugging log

        # Decode QR Code
        image = cv2.imread(file_path)
        decoded_objects = pyzbar.decode(image)

        if not decoded_objects:
            return jsonify({"status": "error", "message": "Invalid QR code"}), 400

        qr_text = decoded_objects[0].data.decode("utf-8")

        # Extract order_id from URL
        if "order_id=" in qr_text:
            order_id = qr_text.split("order_id=")[-1]
        else:
            return jsonify({"status": "error", "message": "Invalid QR code format"}), 400

        # Update order status in the database
        conn = get_db_connection()
        cursor = conn.cursor()

        update_query = "UPDATE deliver SET delivered = 1 WHERE order_id = %s"
        cursor.execute(update_query, (order_id,))
        conn.commit()

        cursor.close()
        conn.close()

        return jsonify({"status": "success", "message": f"Delivery confirmed for Order ID {order_id}"})

    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5001)
