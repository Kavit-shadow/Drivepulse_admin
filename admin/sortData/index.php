<?php

include('../../includes/authentication.php');
authenticationAdmin('../../');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filter data</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css" rel="stylesheet">
        <link rel="shortcut icon" type="image/png" href="../../assets/logo.png" />


    <style>
:root {
    --primary-color: #1e293b; /* Dark Blue-Gray */
    --secondary-color: #334155; /* Slate Gray */
    --accent-color: #64748b; /* Steel Blue */
    --background-color: #0f172a; /* Midnight Blue */
    --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); /* Deeper shadow for darker theme */

    /* Additional background colors */
    --background-light: #1c2a39; /* Slightly lighter background for contrast */
    --background-dark: #0d1117; /* Ultra dark for headers/footers */
    --background-muted: #252f3f; /* Muted tone for sections */
    --background-accent: #16222e; /* Subtle accent background */
}



        body {
            font-family: 'Inter', sans-serif;
            background-color: #eee;
            margin: 0;
            padding: 0;
        }

        #content {
            position: sticky;
            top: 0;
            z-index: 1000;
            background-color: white;
            box-shadow: var(--card-shadow);
        }

        nav {
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            /* border-radius: 0 0 1rem 1rem; */
        }

        .navbar-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .btn-s {
            display: flex;
            gap: 1rem;
        }

        .home-link {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .home-link:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .text h3 {
            color: white;
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .container.box {
            background: white;
            border-radius: 1rem;
            box-shadow: var(--card-shadow);
            padding: 2rem;
            margin-top: 2rem;
            margin-bottom: 2rem;
        }

        .input-daterange {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .form-control {
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
        }

        .btn-info {
            background-color: var(--accent-color);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-info:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            color: #f5f5f5;
        }

        .table {
            border-radius: 0.5rem;
            overflow: hidden;
            margin-top: 1rem;
        }

        .table thead th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
            border: none;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(67, 97, 238, 0.05);
        }

        @media (max-width: 768px) {
            .navbar-container {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .btn-s {
                width: 100%;
                justify-content: center;
            }

            .container.box {
                padding: 1rem;
            }

            .input-daterange {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .home-link {
                padding: 0.5rem;
                font-size: 0.875rem;
            }

            .text h3 {
                font-size: 1.25rem;
            }
        }


          /* Add new styles for resizable columns and text truncation */
          .table th {
            position: relative;
            min-width: 120px; /* Minimum width for columns */
        }

        .table th.resizable {
            resize: horizontal;
            overflow: auto;
        }

        .table td {
            max-width: 200px; /* Maximum width for cells */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .table td.expanded {
            white-space: normal;
            overflow: visible;
        }

        /* Responsive styles for mobile and tablet */
        @media (max-width: 1024px) {
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table th {
                min-width: 100px;
            }

            .input-daterange {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .table th {
                min-width: 80px;
            }

            .table td {
                max-width: 150px;
            }
        }

        /* Add styles for the column resize handle */
        .column-resizer {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 5px;
            cursor: col-resize;
            background-color: transparent;
        }

        .column-resizer:hover {
            background-color: var(--accent-color);
        }
    </style>
</head>

<body>
    <section id="content">
        <nav>
            <div class="navbar-container">
                <div class="btn-s">
                    <a href="../" class="home-link">
                        <i class='bx bx-arrow-back'></i> Back
                    </a>
                    <a href="exportdata?export=true" class="home-link" style="background: rgba(40, 167, 69, 0.2);">
                        <i class='bx bxs-file-blank'></i> Export As Excel
                    </a>
                </div>
                <span class="text">
                    <h3><?php 
                       include("../../configWeb.php");
                    echo $WebAppTitle; ?></h3>
                </span>
            </div>
        </nav>
    </section>

    <div class="container box">
        <div class="table-responsive">
            <div class="row">
                <div class="input-daterange">
                    <div>
                        <input type="text" name="start_date" id="start_date" class="form-control" placeholder="Start Date" />
                    </div>
                    <div>
                        <input type="text" name="end_date" id="end_date" class="form-control" placeholder="End Date" />
                    </div>
                    <div>
                        <button type="button" name="search" id="search" class="btn btn-info w-100">
                            <i class='bx bx-search'></i> Search
                        </button>
                    </div>
                </div>
            </div>
            <table id="order_data" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Total Amount</th>
                        <th>Paid Amount</th>
                        <th>Due Amount</th>
                        <th>Vehicle</th>
                        <th>Trainer Name</th>
                        <th>Admission Date</th>
                        <th>Days</th>
                        <th>Ends On</th>
                        <th>View</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Add new scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdn.datatables.net/colreorder/1.5.4/js/dataTables.colReorder.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize datepicker
            $('.input-daterange').datepicker({
                todayBtn: 'linked',
                format: "yyyy-mm-dd",
                autoclose: true,
                todayHighlight: true
            });

            // Function to make columns resizable
            function makeResizable(table) {
                const cols = table.find('th');
                cols.each(function() {
                    const col = $(this);
                    const resizer = $('<div class="column-resizer"></div>');
                    col.append(resizer);
                    col.addClass('resizable');

                    resizer.on('mousedown', function(e) {
                        const startX = e.pageX;
                        const startWidth = col.width();

                        $(document).on('mousemove', function(e) {
                            const width = startWidth + (e.pageX - startX);
                            col.width(width);
                        });

                        $(document).on('mouseup', function() {
                            $(document).off('mousemove mouseup');
                        });

                        return false;
                    });
                });
            }

            // Initialize DataTable with enhanced features
            function fetch_data(is_date_search, start_date = '', end_date = '') {
                var dataTable = $('#order_data').DataTable({
                    processing: true,
                    serverSide: true,
                    order: [],
                    colReorder: true,
                    responsive: true,
                    ajax: {
                        url: "fetch.php",
                        type: "POST",
                        data: {
                            is_date_search: is_date_search,
                            start_date: start_date,
                            end_date: end_date
                        }
                    },
                    language: {
                        processing: '<i class="bx bx-loader bx-spin"></i> Loading...'
                    },
                    columnDefs: [{
                        targets: '_all',
                        render: function(data, type, row) {
                            if (type === 'display' && data != null) {
                                return '<span title="' + data + '">' + data + '</span>';
                            }
                            return data;
                        }
                    }],
                    drawCallback: function() {
                        makeResizable($('#order_data'));
                    }
                });

                // Add click handler for expanding truncated text
                $('#order_data').on('click', 'td', function() {
                    $(this).toggleClass('expanded');
                });
            }

            fetch_data('no');

            // Search button handler
            $('#search').click(function() {
                var start_date = $('#start_date').val();
                var end_date = $('#end_date').val();
                if (start_date != '' && end_date != '') {
                    $('#order_data').DataTable().destroy();
                    fetch_data('yes', start_date, end_date);
                } else {
                    alert("Both dates are required");
                }
            });
        });
    </script>
</body>

</html>