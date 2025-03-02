import mysql.connector
from flask import Flask, request, jsonify

app = Flask(__name__)

# MySQL Connection
db_config = {
    "host": "localhost",
    "user": "root",
    "password": "",
    "database": "pos_system",
}

@app.route("/api/confirm_delivery", methods=["GET"])
def confirm_delivery():
    order_id = request.args.get("order_id")

    if not order_id:
        return jsonify({"status": "error", "message": "Order ID required"}), 400

    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor()

        # Update order status to delivered
        cursor.execute("UPDATE sales_and_delivery SET status='delivered' WHERE order_id=%s", (order_id,))
        conn.commit()

        cursor.close()
        conn.close()

        return jsonify({"status": "success", "message": "Order marked as delivered!"})

    except Exception as e:
        return jsonify({"status": "error", "message": str(e)})

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000, debug=True)
