<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html5-qrcode@0.4.0/dist/html5-qrcode.min.js"></script>
    <style>
        .details {
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading"><?php echo e(__('QR Code Scanner'), false); ?></div>

                    <div class="panel-body">
                        <form id="myform" method="POST">
                            <?php echo e(csrf_field(), false); ?>

                            <div class="input-group">
                                <input type="text" name="lot_number" id="lot_number" class="form-control"
                                    placeholder="Enter lot number">
                                <div class="input-group-btn">
                                    <button class="btn btn-primary" id="btnscan" type="button"
                                        onclick="startScan()">Scan QR Code</button>
                                </div>
                            </div>
                            <div id="qr-reader" style="width: 100%; margin-top: 20px;"></div>
                            <p id="scan-error" style="color: red; display: none;">QR code scanning failed. Please
                                ensure the QR code is visible and try again.</p>
                                <div class="button-container">
                                <button type="button" class="btn btn-primary mt-3" data-toggle="modal"
                                    data-target="#trackModal" onclick="openModal('trackModal')">Track</button>
                                <button type="button" class="btn btn-primary mt-3" data-toggle="modal"
                                    data-target="#traceModal" onclick="openModal('traceModal')">Trace</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/html5-qrcode/dist/html5-qrcode.min.js"></script>
        <script>
            let html5QrCode;
            let qrCodeSuccessCallback;
            let selectedModal;

            function startScan() {
                html5QrCode = new Html5Qrcode("qr-reader");
                qrCodeSuccessCallback = (decodedText, decodedResult) => {
                    console.log(`Scan result: ${decodedText}`, decodedResult);
                    document.getElementById('lot_number').value = decodedText;
                    openModal(decodedText);
                    html5QrCode.stop();
                };

                const qrCodeErrorCallback = (error) => {
                    console.error(error);
                    document.getElementById('scan-error').style.display = 'block';
                };

                const config = {
                    fps: 10,
                    qrbox: 250
                };
                html5QrCode.start({
                    facingMode: "environment"
                }, config, qrCodeSuccessCallback, qrCodeErrorCallback);
            }

            function openModal(modal) {
        
                // Determine which modal to open based on the selected button
                if (modal === "traceModal") {
                 console.log('hi im tracking');
                    // Submit to track endpoint and load data into the modal
                  // Send the AJAX request
                    $.ajax({
                    url: "/trace", 
                    type: "POST",
                    data: new FormData($("#myform")[0]), 
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Handle the response from the backend
                        console.log(response);

                        // Access and use the returned data  
                        document.getElementById("result-container").textContent = response.id;
                        document.getElementById("crop_variety").textContent = response.crop_variety_text;
                        document.getElementById("lab_test_number").textContent = response.lab_test_number;
                        document.getElementById("lot_number").textContent = response.lot_number;
                        document.getElementById("mother_lot").textContent = response.mother_lot;
                        document.getElementById("p_x_g").textContent = response.p_x_g;
                        document.getElementById("packaging").textContent = response.packaging;
                        document.getElementById("quantity").textContent = response.quantity;
                        document.getElementById("weight").textContent = response.sample_weight;

                        if (response.report_recommendation == 11) {
                            document.getElementById("status").textContent = "Marketable";
                        } else {
                            document.getElementById("status").textContent = "Not Marketable";
                        }
                        document.getElementById("test").textContent = response.tests_required;
                    },
                    error: function(error) {
                        // Handle the error
                        console.error(error);
                    }
                });


                } else if (modal === "trackModal") {
                console.log('hi im tracing');
                    // Submit to track endpoint and load data into the modal
                    lot_number = new FormData($("#myform")[0]);
                    $.ajax({
                        url: "/track", 
                        type: "POST",
                        data: lot_number, 
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            // Handle the response from the backend and populate the modal
                            console.log(response);
                            var lot_number = $("#lot_number").val(); 
                            var tableHTML = "<table>";
                        
                            tableHTML += "<tbody>";
                            
                            response.forEach(function(item) {
                                tableHTML += "<tr>";
                                tableHTML += "<td>"+ lot_number + "</td>";
                                tableHTML += "<td><a href='http://127.0.0.1:8000/admin/seed-labs/" + item.id + "' onclick='closeModalAndRedirect(this)' data-dismiss='modal'>" + item.lot_number + "</a></td>";

                                tableHTML += "</tr>";
                            });
                            
                            tableHTML += "</tbody></table>";
                            
                            document.getElementById("trace_result-container").innerHTML = tableHTML;
                        },
                            
                        error: function(error) {
                            // Handle the error
                            console.error(error);
                        }
                    });
                }
            }
            function closeModalAndRedirect(link) {
                window.location.href = link.href; // Redirect to the link
            }

      
        </script>

    </div>

    <!-- Trace Modal -->
    <div class="modal fade" id="traceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <!-- Trace Modal content goes here -->
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Batch details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#home">Seed Details</a></li>
                        <li><a data-toggle="tab" href="#profile">Seed Lab Details</a></li>
                        <li><a data-toggle="tab" href="#contact">Mother Lot</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="home">
                            <div class="card">
                                <div class="card-body">
                                    <div class="details">
                                        <strong>Id:</strong>
                                        <p class="text-muted" id="result-container"></p>
                                    </div>
                                    <div class="details">
                                        <strong>Crop Variety:</strong>
                                        <p class="text-muted" id="crop_variety"></p>
                                    </div>
                                    <div class="details">
                                        <strong>Lot Number:</strong>
                                        <p class="text-muted" id="lot_number"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="profile">
                            <div class="card">
                                <div class="card-body">
                                    <div class="details">
                                        <strong>Lab Test Number:</strong>
                                        <p class="text-muted" id="lab_test_number"></p>
                                    </div>
                                    <div class="details">
                                        <strong>P_x_G:</strong>
                                        <p class="text-muted" id="p_x_g"></p>
                                    </div>
                                    <div class="details">
                                        <strong>Packaging:</strong>
                                        <p class="text-muted" id="packaging"></p>
                                    </div>
                                    <div class="details">
                                        <strong>Tests Made:</strong>
                                        <p class="text-muted" id="test"></p>
                                    </div>
                                    <div class="details">
                                        <strong>Status:</strong>
                                        <p class="text-muted" id="status"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="contact">
                            <div class="card">
                                <div class="card-body">
                                    <div class="details">
                                        <strong>Mother Lot:</strong>
                                        <p class="text-muted" id="mother_lot"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

<!-- Track Modal -->
<div class="modal fade" id="trackModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Batch details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Mother Lot</th>
                            <th>Child Lot</th>
                        </tr>
                    </thead>
                    <tbody id="trace_result-container">
                        <!-- Table rows will be dynamically populated here -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


</body>

</html>


          <?php /**PATH C:\Users\Cole\Desktop\stts\resources\views/track_and_trace/track_trace_form.blade.php ENDPATH**/ ?>