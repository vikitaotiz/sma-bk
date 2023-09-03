<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>System Notification Mail</title>
        <style>
            body,
            html {
                height: 100%;
                padding: 0px;
                margin: 0px;
                font-family: Arial, Helvetica, sans-serif;
            }

            #main {
                display: grid;
                grid-template-rows: 1fr 10fr 1fr;
                height: 100vh;
                border: 1px solid #017951;
            }

            #header {
                background-color: #017951;
                color: white;
                display: flex;
                text-align: center;
                justify-content: center;
                align-items: center;
                padding: 10px;
            }

            #content {
                padding: 20px;
            }

            #footer {
                background-color: rgb(82, 81, 81);
                color: white;
                display: flex;
                text-align: center;
                justify-content: center; /* Align horizontal */
                align-items: center;
                padding: 10px;
            }

            .pad {
                padding: 10px;
            }
        </style>
    </head>
    <body>
        <div id="main">
            <div id="header">
                <h2>SALES MANAGEMENT APP (SMA)</h2>
            </div>
            <div id="content">
                Hello Admin, <br />
                <br />
                <div>
                    This is to notify you that a sale has been made. <br />
                    <br />
                    <strong>Details:</strong> <br><br>

                    <table
                        border="1"
                        style="width: 100%; border-collapse: collapse"
                    >
                        <tr class="pad">
                            <th>BILL-{{ $data['bill']['bill_ref'] }}</th>
                        </tr>
                        <tr class="pad">
                            <th>Created by: {{ $data['bill']['user']['name'] }}</th>
                        </tr>
                        <tr class="pad">
                            <th>
                                Created on: {{ $data['bill']['created_at'] }}
                            </th>
                        </tr>
                        <tr class="pad">
                            <th>Mode: {{ $data['bill']['payment_mode']['name'] }}</th>
                        </tr>
                        <tr class="pad">
                            <th>
                                Selling Price: {{ $data['bill']['actual_selling_price'] }}
                            </th>
                        </tr>
                        <tr class="pad">
                            <th>Status: {{ $data['bill']['status'] }}</th>
                        </tr>

                        <tr class="pad">
                            <th>Department: {{ $data['bill']['department']['name'] }}</th>
                        </tr>

                        <tr class="pad">
                            <th>
                                Debtor Name: {{ $data['bill']['debtor_name'] }}
                            </th>
                        </tr>

                        <tr class="pad">
                            <th>
                                Total debt paid: {{ $data['bill']['total_debt_paid']}}
                            </th>
                        </tr>
                    </table>
                </div>
            </div>
            <div id="footer">Sales Management Application</div>
        </div>
    </body>
</html>
