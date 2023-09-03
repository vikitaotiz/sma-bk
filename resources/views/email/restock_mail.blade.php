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
                <div>
                    <h2>SALES MANAGEMENT APP (SMA)</h2>
                    <hr/>
                    <strong>Product Restock Alert</strong>
                </div>
            </div>
            <div id="content">
                Hello Admin, <br />
                <br />
                <div>
                    This is to notify you that <strong>{{$data['data']->name}}</strong> has reached its minimum quantity. <br />
                    <br />
                    <strong>Details:</strong> <br><br>
                    <table
                        border="1"
                        style="width: 100%; border-collapse: collapse"
                    >
                        <tr class="pad">
                            <th>Product Name: {{$data['data']->name}}</th>
                        </tr>
                        <tr class="pad">
                            <th>Available Quantity: {{$data['data']->quantity}}</th>
                        </tr>
                        <tr class="pad">
                            <th>Reported on: {{$data['data']->updated_at}}</th>
                        </tr>
                    </table>
                </div>
            </div>
            <div id="footer">Sales Management Application</div>
        </div>
    </body>
</html>
