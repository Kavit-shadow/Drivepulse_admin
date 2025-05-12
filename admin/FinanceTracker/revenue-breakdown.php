<?php
require_once "../../config.php";
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_ID'])) {
    header("Location: ../../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revenue Breakdown - Finance Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#007bff',
                        secondary: '#6c757d',
                        success: '#28a745',
                        danger: '#dc3545'
                    }
                }
            }
        }
    </script>
    <style>
        .breakdown-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .breakdown-card:hover {
            transform: translateY(-5px);
        }

        .navigation-hint {
            background-color: #f8f9fa;
            border-left: 4px solid #4299e1;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            border-radius: 0.375rem;
        }

        .header {
            position: relative;
            top: 0;
            left: 0;
            right: 0;
            height: var(--header-height);
            background: linear-gradient(135deg, #1e293b, #334155);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 0;
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
        }

        .back-button {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            cursor: pointer;
            font-size: 1.25rem;
            color: white;
            padding: 0.5rem;
            margin-right: 1rem;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s;
        }

        .back-button:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .header-logo {
            height: 40px;
            margin-right: 1rem;
        }

        .page-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: white;
            flex-grow: 1;
        }
    </style>
</head>

<body class="bg-gray-50">
    <header class="header">
        <button class="back-button" onclick="window.location.href='./';">
            <i class="fas fa-arrow-left"></i>
        </button>
        <img src="https://patelmotordrivingschool.com/storage/images/pmds-assets/pmds-text-pure-w-L.png" alt="PMDS Logo" class="header-logo">
    </header>

    <div class="container mx-auto px-4 mt-4">
        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="flex text-sm" id="navigationBreadcrumb">
                    <li class="text-blue-600 hover:text-blue-800"><a href="./">Finance Tracker</a></li>
                    <li class="mx-2">/</li>
                    <li class="text-gray-600">Revenue Breakdown</li>
                </ol>
            </nav>
        </div>

        <div class="navigation-hint">
            <p><i class="fas fa-info-circle mr-2"></i> <strong>Navigation Tips:</strong></p>
            <ul class="ml-6 mt-1 list-disc">
                <li>Press <kbd class="bg-gray-200 px-1 rounded">Backspace</kbd> to go back to the previous page</li>
                <li>Clickable items: months, weeks, days, and individual transaction cards</li>
            </ul>
        </div>

        <div class="mb-6">
            <div class="w-full md:w-1/4">
                <label for="yearSelect" class="block text-sm font-medium text-gray-700">Select Year</label>
                <select class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 transition duration-150 ease-in-out" id="yearSelect">
                    <option value="" disabled selected>Select a year</option>
                    <!-- Options will be populated dynamically -->
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6 hover:shadow transition-shadow duration-300 flex items-center border border-gray-200">
                <div class="flex-shrink-0">
                    <i class="fas fa-dollar-sign text-4xl text-green-600"></i>
                </div>
                <div class="ml-4">
                    <div class="text-sm text-gray-600">Total Revenue</div>
                    <div class="text-2xl font-bold" id="totalRevenue">₹0.00</div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 hover:shadow transition-shadow duration-300 flex items-center border border-gray-200">
                <div class="flex-shrink-0">
                    <i class="fas fa-money-bill-wave text-4xl text-red-600"></i>
                </div>
                <div class="ml-4">
                    <div class="text-sm text-gray-600">Total Expenses</div>
                    <div class="text-2xl font-bold" id="totalExpenses">₹0.00</div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 hover:shadow transition-shadow duration-300 flex items-center border border-gray-200">
                <div class="flex-shrink-0">
                    <i class="fas fa-chart-line text-4xl text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <div class="text-sm text-gray-600">Net Profit</div>
                    <div class="text-2xl font-bold" id="netProfit">₹0.00</div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 hover:shadow transition-shadow duration-300 flex items-center border border-gray-200">
                <div class="flex-shrink-0">
                    <i class="fas fa-list-alt text-4xl text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <div class="text-sm text-gray-600">Transaction Count</div>
                    <div class="text-2xl font-bold" id="transactionCount">0</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6">
                <div id="breakdownData" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow mt-4" id="dayTransactionCard" style="display: none;">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-3">Transactions for <span id="selectedDay"></span></h3>
                <div id="transactionList" class="grid grid-cols-1 gap-6"></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Get URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const year = urlParams.get('year') || new Date().getFullYear();
            const month = urlParams.get('month');
            const week = urlParams.get('week');
            const day = urlParams.get('day');

            // Load initial data
            loadBreakdownData(year, month, week, day);

            // Add keyboard navigation
            $(document).keydown(function(e) {
                // Only Backspace key (8)
                if (e.keyCode === 8) {
                    // Prevent default action for backspace to avoid browser back
                    if (!$(e.target).is('input, textarea')) {
                        e.preventDefault();
                        goBack();
                    }
                }
            });

            function goBack() {
                // If we're on a day view, go back to week
                if (day) {
                    window.location.href = `revenue-breakdown.php?year=${year}&month=${month}&week=${week}`;
                }
                // If we're on a week view, go back to month
                else if (week) {
                    window.location.href = `revenue-breakdown.php?year=${year}&month=${month}`;
                }
                // If we're on a month view, go back to year
                else if (month) {
                    window.location.href = `revenue-breakdown.php?year=${year}`;
                }
                // If we're on a year view, go back to main page
                else {
                    window.location.href = './';
                }
            }

            // Year select change handler
            $('#yearSelect').on('change', function() {
                const selectedYear = $(this).val();
                window.location.href = `revenue-breakdown.php?year=${selectedYear}`;
            });

            function loadBreakdownData(year, month, week, day) {
                let url = `api.php?action=revenue_breakdown&year=${year}`;
                if (month) url += `&month=${month}`;
                if (week) url += `&week=${week}`;
                if (day) url += `&day=${day}`;

                $.get(url)
                    .done(function(response) {
                        if (response.success) {
                            updateBreadcrumb(response.current_filters);
                            populateYearSelect(response.available_years, response.current_filters.year);
                            displayBreakdownData(response.data, response.current_filters);
                            updateSummaryStats(response.data);
                        } else {
                            alert('Error loading data: ' + response.error);
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        console.error('Error:', errorThrown);
                        alert('Failed to load data. Please try again later.');
                    });
            }

            function updateBreadcrumb(filters) {
                const breadcrumb = $('#navigationBreadcrumb');
                breadcrumb.html('<li class="text-blue-600 hover:text-blue-800"><a href="index.php">Finance Tracker</a></li>');

                let url = 'revenue-breakdown.php';
                breadcrumb.append(`<li class="mx-2">/</li><li class="text-gray-600">Revenue Breakdown</li>`);

                if (filters.year) {
                    url += `?year=${filters.year}`;
                    breadcrumb.append(`<li class="mx-2">/</li><li class="text-gray-600"><a href="${url}">${filters.year}</a></li>`);
                }

                if (filters.month) {
                    url += `&month=${filters.month}`;
                    const monthName = new Date(filters.year, filters.month - 1).toLocaleString('default', {
                        month: 'long'
                    });
                    breadcrumb.append(`<li class="mx-2">/</li><li class="text-gray-600"><a href="${url}">${monthName}</a></li>`);
                }

                if (filters.week) {
                    url += `&week=${filters.week}`;
                    breadcrumb.append(`<li class="mx-2">/</li><li class="text-gray-600"><a href="${url}">Week ${filters.week}</a></li>`);
                }

                if (filters.day) {
                    breadcrumb.append(`<li class="mx-2">/</li><li class="text-gray-600">${filters.day}</li>`);
                }
            }

            function populateYearSelect(years, selectedYear) {
                const yearSelect = $('#yearSelect');
                yearSelect.empty();
                years.forEach(year => {
                    yearSelect.append(new Option(year, year, year == selectedYear, year == selectedYear));
                });
            }

            function displayBreakdownData(data, filters) {
                const container = $('#breakdownData');
                container.empty();

                console.log('Response Data:', data); // Log the response data for debugging

                if (!data || data.length === 0) {
                    container.html(`
                        <div class="col-span-3 text-center py-8">
                            <div class="text-gray-500">
                                <i class="fas fa-info-circle text-2xl mb-2"></i>
                                <p>No data available for the selected period.</p>
                            </div>
                        </div>
                    `);
                    return;
                }

                if (filters.day) {
                    data.forEach(item => {
                        const transactionCard = `
                            <div class="bg-white rounded-lg shadow-lg mb-4 cursor-pointer view-customer transition-transform transform hover:scale-103 hover:shadow-xl hover:border-l-4 hover:border-blue-500" data-transaction_id="${item.transaction_id}" data-customer_name="${item.customer_name}" data-amount="${item.amount}" data-transaction_date="${item.transaction_date}" data-transaction_time="${item.transaction_time}" data-vehicle="${item.vehicle}" data-customer_phone="${item.customer_phone}" onclick="sendToView('${item.transaction_id}')">
                                <div class="p-4">
                                    <h4 class="font-semibold text-lg">Transaction ID: <span class="text-blue-600">${item.transaction_id}</span></h4>
                                    <p class="text-gray-700"><strong>${item.category_name ? 'Category Name' : 'Customer Name'}:</strong> <span class="font-medium">${item.category_name || item.customer_name || 'N/A'}</span></p>
                                    <p class="text-gray-800"><strong>Amount:</strong> <span class="font-bold ${item.type === 'income' ? 'text-green-600' : (item.type === 'expense' ? 'text-red-600' : 'text-gray-600')}">₹${parseFloat(item.amount).toLocaleString('en-IN', {minimumFractionDigits: 2})}</span></p>
                                    <p class="text-gray-600"><strong>Date:</strong> <span class="italic">${new Date(item.transaction_date).toLocaleDateString()}</span></p>
                                    ${item.transaction_time ? `<p class="text-gray-600"><strong>Time:</strong> <span class="italic">${item.transaction_time}</span></p>` : ''}
                                    ${item.description ? `<p class="text-gray-600"><strong>Description:</strong> <span class="italic">${item.description}</span></p>` : ''}
                                    ${item.vehicle ? `<p class="text-gray-600"><strong>Vehicle:</strong> <span class="italic">${item.vehicle}</span></p>` : ''}
                                    ${item.customer_phone ? `<p class="text-gray-600"><strong>Phone:</strong> <span class="italic">${item.customer_phone}</span></p>` : ''}
                                    <hr class="border-t-2 border-gray-300 my-4">
                                    <span class="inline-block bg-gray-200 text-gray-800 text-sm font-semibold px-2 py-1 rounded-full">${item.type || 'income'}</span>
                                </div>
                            </div>`;
                        container.append(transactionCard);
                    });
                } else {
                    data.forEach(item => {
                        let title, url;
                        if (!filters.month) {
                            title = new Date(filters.year, item.month - 1).toLocaleString('default', {
                                month: 'long'
                            });
                            url = `revenue-breakdown.php?year=${filters.year}&month=${item.month}`;
                        } else if (!filters.week) {
                            title = `Week ${item.week}`;
                            url = `revenue-breakdown.php?year=${filters.year}&month=${filters.month}&week=${item.week}`;
                        } else {
                            title = new Date(item.date).toLocaleDateString();
                            url = `revenue-breakdown.php?year=${filters.year}&month=${filters.month}&week=${filters.week}&day=${item.date}`;
                        }

                        const revenue = parseFloat(item.revenue) || 0; // Default to 0 if undefined
                        const profit = parseFloat(item.profit) || 0; // Default to 0 if undefined

                        const card = `
                            <div class="breakdown-card bg-white rounded-lg shadow-md hover:shadow-lg">
                                <div class="p-4" onclick="window.location.href='${url}'">
                                    <h5 class="text-lg font-semibold mb-3">${title}</h5>
                                    <div class="grid grid-cols-2 gap-4 mb-2">
                                        <div>
                                            <div class="text-sm text-gray-600">Revenue</div>
                                            <p class="font-medium">₹${revenue.toLocaleString('en-IN', {minimumFractionDigits: 2})}</p>
                                        </div>
                                        <div>
                                            <div class="text-sm text-gray-600">Profit</div>
                                            <p class="font-medium ${profit >= 0 ? 'text-green-600' : 'text-red-600'}">
                                                ₹${profit.toLocaleString('en-IN', {minimumFractionDigits: 2})}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-500">${item.transaction_count || 0} transactions</div>
                                </div>
                            </div>
                        `;
                        container.append(card);
                    });
                }
            }

            function updateSummaryStats(data) {
                let totalRevenue = 0;
                let totalExpenses = 0;
                let totalTransactions = 0;

                if (data && data.length > 0) {
                    data.forEach(item => {
                        console.log(item);
                        if (item.source == 'customer') {
                            console.log(item.source);
                            totalRevenue += parseFloat(item.amount) || 0; // Sum all revenues
                            totalExpenses += 0; // Sum all expenses
                            totalTransactions += 1; // Count all transactions
                        } else if (item.source == 'transaction') {
                            if (item.type === 'income') {
                                totalRevenue += parseFloat(item.amount) || 0; // Sum all revenues
                            } else {
                                totalExpenses += parseFloat(item.amount) || 0; // Sum all expenses
                            }
                            totalTransactions += 1; // Count all transactions
                        } else {
                            totalRevenue += parseFloat(item.revenue) || 0; // Sum all revenues
                            totalExpenses += parseFloat(item.expenses) || 0; // Sum all expenses
                            totalTransactions += parseInt(item.transaction_count) || 0; // Count all transactions
                        }
                    });
                }

                const netProfit = totalRevenue - totalExpenses;

                $('#totalRevenue').text('₹' + totalRevenue.toLocaleString('en-IN', {
                    minimumFractionDigits: 2
                }));
                $('#totalExpenses').text('₹' + totalExpenses.toLocaleString('en-IN', {
                    minimumFractionDigits: 2
                }));
                $('#netProfit')
                    .text('₹' + netProfit.toLocaleString('en-IN', {
                        minimumFractionDigits: 2
                    }))
                    .removeClass('text-green-600 text-red-600')
                    .addClass(netProfit >= 0 ? 'text-green-600' : 'text-red-600');
                $('#transactionCount').text(totalTransactions);
            }
            function sendToView(transaction_id, customer_name, amount, transaction_date, transaction_time, vehicle, customer_phone) {
                // Assuming transaction_id is an object containing the necessary data

                if (!customer_phone) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Unable to View',
                        text: "This entry pertains to a transaction and does not correspond to a customer profile.",
                    });
                    return;
                }

                const url = `../view?id=${transaction_id}&phone=${customer_phone}&date=${transaction_date}&route=${encodeURIComponent(window.location.href)}`;
                window.location.href = url;
            }

            // Call loadDayTransactions when a day is selected
            $(document).on('click', '.view-customer', function() {
                const transaction_id = $(this).data('transaction_id');
                const customer_name = $(this).data('customer_name');
                const amount = $(this).data('amount');
                const transaction_date = $(this).data('transaction_date');
                const transaction_time = $(this).data('transaction_time');
                const vehicle = $(this).data('vehicle');
                const customer_phone = $(this).data('customer_phone');
                sendToView(transaction_id, customer_name, amount, transaction_date, transaction_time, vehicle, customer_phone);
            });
        });
    </script>
</body>

</html>