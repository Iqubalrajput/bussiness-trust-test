<!DOCTYPE html>
<html>
<head>
    <title>Salary Slip</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
        }
        .header h1 {
            font-size: 24px;
            margin: 0;
        }
        .salary-details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .salary-details th, .salary-details td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .salary-details th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .total {
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Salary Slip for [Salary Date]</h1>
        <p>Employee ID: [Employee ID]</p>
    </div>
    
    <table class="salary-details">
        <tr>
            <th>Amount</th>
            <th>Status</th>
            <th>Salary Date</th>
        </tr>
        <tr>
            <td>₹ [Amount]</td>
            <td>[Status]</td>
            <td>[Salary Date]</td>
        </tr>
    </table>
    
    <div class="total">
        <p>Total Payable: ₹ [Amount]</p>
    </div>
</body>
</html>
