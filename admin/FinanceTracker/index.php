<!DOCTYPE html>
<html lang="en">
<?php

include('../../includes/authentication.php');
authenticationAdmin('../../');

?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
    <link rel="shortcut icon" type="image/png" href="../../assets/logo.png" />

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            --secondary-gradient: linear-gradient(135deg, #ff6a88 0%, #ff6a88 100%);
        }

        body {
            background-color: #f4f7fa;
        }

        .custom-shadow {
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 1024px) {
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            
            .chart-controls {
                flex-direction: column;
                gap: 1rem;
            }
            
            .chart-controls select {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .responsive-flex {
                flex-direction: column;
                gap: 1rem;
            }

            .responsive-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .mobile-full-width {
                width: 100% !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                margin-bottom: 1rem;
            }

            .header {
                padding: 0.75rem;
            }

            .header-logo {
                height: 32px;
            }

            .page-title {
                font-size: 1.125rem;
            }

            /* Adjust form controls spacing */
            #filterForm, #exportForm {
                gap: 0.75rem;
            }

            /* Make buttons more touch-friendly */
            button {
                min-height: 44px;
                padding: 0.75rem 1rem;
            }

            /* Adjust table for mobile */
            .table-container {
                margin: 0 -1rem;
                padding: 0 1rem;
                overflow-x: auto;
            }

            /* Improve chart responsiveness */
            .chart-container {
                height: 300px;
            }

            /* Adjust modal width */
            .modal-content {
                width: 95%;
                max-height: 90vh;
                overflow-y: auto;
            }
        }

        @media (max-width: 480px) {
            .text-4xl {
                font-size: 1.875rem;
            }

            .summary-card {
                padding: 1rem;
            }

            .chart-stats {
                grid-template-columns: 1fr;
            }

            .header{
                overflow: auto;
            }
        }

        @media (min-width: 768px) {
            .chart-container {
                height: 400px;
            }
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

<body class="font-sans antialiased">

    <header class="header">
        <button class="back-button">
            <i class="fas fa-arrow-left"></i>
        </button>
        <img src="https://patelmotordrivingschool.com/storage/images/pmds-assets/pmds-text-pure-w-L.png" alt="PMDS Logo" class="header-logo">
            <div class="ml-auto w-full sm:w-auto">
                <button onclick="window.location.href='revenue-breakdown.php'" class="btn btn-primary bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-200 w-full text-sm md:text-base">
                    Revenue Breakdown
                </button>
            </div>
    </header>

    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <header class="flex justify-between items-center mb-10 responsive-flex">
            <div>
                <h1 class="text-4xl font-bold text-gray-800">Finance Tracker</h1>
                <p class="text-gray-600">Your Personal Financial Dashboard</p>
            </div>
            <div class="flex space-x-4">
                <button id="addTransactionBtn" class="bg-purple-600 text-white px-6 py-2 rounded-full hover:bg-purple-700 transition transform hover:scale-105">
                    <i class="fas fa-plus mr-2"></i>New Transaction
                </button>
            </div>
        </header>

        <!-- Financial Summary -->
        <div id="summarySection" class="mb-8">
            <!-- Payment methods breakdown will be inserted here -->
            
            <!-- Existing summary content -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl p-6 custom-shadow hover:shadow-lg transition">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-600">Total Income</h3>
                        <span class="text-sm income-change">
                            <!-- Will be populated by JS -->
                        </span>
                    </div>
                    <p class="text-3xl font-bold text-green-600 total-income">₹0</p>
                </div>
                <div class="bg-white rounded-2xl p-6 custom-shadow hover:shadow-lg transition">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-600">Total Expenses</h3>
                        <span class="text-sm expense-change">
                            <!-- Will be populated by JS -->
                        </span>
                    </div>
                    <p class="text-3xl font-bold text-red-600 total-expense">₹0</p>
                </div>
                <div class="bg-white rounded-2xl p-6 custom-shadow hover:shadow-lg transition">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-600">Net Balance</h3>
                        <span class="text-blue-500 text-sm current-month">Current Month</span>
                    </div>
                    <p class="text-3xl font-bold text-blue-600 net-balance">₹0</p>
                </div>
            </div>
        </div>

        <!-- Advanced Filtering -->
        <!--<section class="mb-8">-->
        <!--    <div class="bg-white rounded-2xl p-4 md:p-6 custom-shadow">-->
        <!--        <form id="filterForm" class="flex flex-col md:flex-row gap-4">-->
        <!--            <input type="text" id="dateRangePicker" placeholder="Select Date Range"-->
        <!--                class="p-2 border rounded mobile-full-width flex-grow">-->
        <!--            <select class="p-2 border rounded mobile-full-width" id="categoryFilter">-->
        <!--                <option value="">All Categories</option>-->
                        <!-- Categories will be populated dynamically -->
        <!--            </select>-->
        <!--            <select class="p-2 border rounded mobile-full-width" id="typeFilter">-->
        <!--                <option value="">All Types</option>-->
        <!--                <option value="expense">Expense</option>-->
        <!--                <option value="income">Income</option>-->
        <!--            </select>-->
        <!--            <select class="p-2 border rounded mobile-full-width" id="paymentMethodFilter">-->
        <!--                <option value="">All Payment Methods</option>-->
        <!--                <option value="cash">Cash</option>-->
        <!--                <option value="bank">Bank</option>-->
        <!--            </select>-->
        <!--            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded mobile-full-width hover:bg-purple-700">-->
        <!--                <i class="fas fa-filter mr-2"></i>Apply Filter-->
        <!--            </button>-->
        <!--        </form>-->
        <!--    </div>-->
        <!--</section>-->
        
        <div class="mb-8 bg-white rounded-2xl p-4 md:p-6 custom-shadow"> 
        <div class="mb-4" >
            <h2 class="text-xl font-bold text-gray-800">Transactions Filter</h2>
            <p class="text-sm text-gray-600 mt-1">Filter transactions by date, category, type, and payment method</p>
        </div>
         <section class="mb-8">
            <div class="bg-white rounded-2xl p-4 md:p-6 ">
                <form id="filterForm" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <input type="text" id="dateRangePicker" placeholder="Select Date Range"
                        class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-600">
                    <select class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-600" id="categoryFilter">
                        <option value="">All Categories</option>
                        <!-- Categories will be populated dynamically -->
                    </select>
                    <select class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-600" id="typeFilter">
                        <option value="">All Types</option>
                        <option value="expense">Expense</option>
                        <option value="income">Income</option>
                    </select>
                    <select class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-600" id="paymentMethodFilter">
                        <option value="">All Payment Methods</option>
                        <option value="cash">Cash</option>
                        <option value="bank">Bank</option>
                    </select>
                    <button type="submit" class="w-full bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-purple-600">
                        <i class="fas fa-filter mr-2"></i>Apply Filter
                    </button>
                </form>
            </div>
        </section>

       

        <!-- Transactions Table -->
        <section class="bg-white rounded-2xl p-4 md:p-6 ">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4 ">
                <h3 class="text-xl font-semibold">Transactions</h3>
                <button id="addCategoryBtn" class="bg-gray-600 text-white px-4 py-2 rounded-full hover:bg-gray-700 transition w-full md:w-auto">
                    <i class="fas fa-tags mr-2"></i>Manage Categories
                </button>
            </div>
            <div class="table-container max-h-96 overflow-auto relative">
                <table class="w-full min-w-max">
                    <thead class="sticky top-0 bg-white z-10">
                        <tr class="bg-gray-100">
                            <th class="p-3 text-left">Date</th>
                            <th class="p-3 text-left">Category</th>
                            <th class="p-3 text-left">Type</th>
                            <th class="p-3 text-left">Method</th>
                            <th class="p-3 text-right">Amount</th>
                            <th class="p-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="transactionsBody">
                        <!-- Transactions will be dynamically populated -->
                    </tbody>
                </table>
            </div>
        </section>
        </div>
        
         <!-- Add this after your filter section -->
        <section class="mb-8" style="display: none;">
            <div class="bg-white rounded-2xl p-4 md:p-6 custom-shadow">
                <h3 class="text-xl font-semibold mb-4">Export Data</h3>
                <form id="exportForm" class="flex flex-col md:flex-row gap-4">
                    <select class="p-2 border rounded mobile-full-width" id="exportType">
                        <option value="all">All Transactions</option>
                        <option value="income">Income Only</option>
                        <option value="expense">Expenses Only</option>
                        <option value="summary">Monthly Summary</option>
                        <option value="category">Category-wise Summary</option>
                    </select>
                    <select class="p-2 border rounded mobile-full-width" id="exportFormat">
                        <option value="excel">Excel (.xlsx)</option>
                        <option value="csv">CSV</option>
                        <option value="pdf">PDF</option>
                    </select>
                    <input type="text" id="exportDateRange" placeholder="Select Date Range"
                        class="p-2 border rounded mobile-full-width flex-grow">
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition">
                        <i class="fas fa-file-export mr-2"></i>Export
                    </button>
                </form>
            </div>
        </section>

        <!-- Expense Chart -->
        <section class="mt-8 bg-white rounded-2xl p-4 md:p-6 custom-shadow">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <h3 class="text-xl font-semibold">Financial Analytics</h3>
                <div class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
                    <select id="chartType" class="p-2 border rounded">
                        <option value="bar">Bar Chart</option>
                        <option value="line">Line Chart</option>
                        <option value="pie">Pie Chart</option>
                    </select>
                    <select id="chartPeriod" class="p-2 border rounded">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                    <select id="chartDataType" class="p-2 border rounded">
                        <option value="all">Income & Expenses</option>
                        <option value="income">Income Only</option>
                        <option value="expense">Expenses Only</option>
                        <option value="category">By Category</option>
                    </select>
                    <select id="chartPaymentMethod" class="p-2 border rounded">
                        <option value="all">All Payment Methods</option>
                        <option value="cash">Cash Only</option>
                        <option value="bank">Bank Only</option>
                    </select>
                </div>
            </div>

            <div class="mb-6 flex flex-col md:flex-row gap-4">
                <input type="text" id="chartDateRange" placeholder="Select Date Range"
                    class="p-2 border rounded flex-grow">
                <button id="applyChartFilters" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 w-full md:w-auto">
                    <i class="fas fa-sync-alt mr-2"></i>Update Chart
                </button>
            </div>

            <div class="chart-container relative mb-20" style="height: 400px;">
                <canvas id="financeChart"></canvas>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-8">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm text-gray-600">Total Income</h4>
                    <p class="text-lg font-semibold chart-total-income">₹0</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm text-gray-600">Total Expenses</h4>
                    <p class="text-lg font-semibold chart-total-expenses">₹0</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm text-gray-600">Net Balance</h4>
                    <p class="text-lg font-semibold chart-net-balance">₹0</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm text-gray-600">Average Daily</h4>
                    <p class="text-lg font-semibold chart-daily-avg">₹0</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm text-gray-600">Cash Transactions</h4>
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        <div>
                            <p class="text-xs text-gray-500">Income</p>
                            <p class="text-lg font-semibold chart-cash-income">₹0</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Expense</p>
                            <p class="text-lg font-semibold chart-cash-expense">₹0</p>
                        </div>
                    </div>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm text-gray-600">Bank Transactions</h4>
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        <div>
                            <p class="text-xs text-gray-500">Income</p>
                            <p class="text-lg font-semibold chart-bank-income">₹0</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Expense</p>
                            <p class="text-lg font-semibold chart-bank-expense">₹0</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- New Transaction Modal -->
    <div id="transactionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-start md:items-center justify-center z-50 p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl p-6 w-full max-w-lg my-8 relative">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800">New Transaction</h2>
                <button type="button" class="text-gray-500 hover:text-gray-700" id="closeModal">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="transactionForm">
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700">Transaction Type</label>
                    <select class="w-full p-3 border rounded-lg" id="transactionType" name="type">
                        <option value="expense">Expense</option>
                        <option value="income">Income</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700">Amount</label>
                    <input type="number" id="transactionAmount" name="amount" class="w-full p-3 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700">Payment Method</label>
                    <select class="w-full p-3 border rounded-lg" id="transactionPaymentMethod" name="payment_method" required>
                        <option value="cash">Cash</option>
                        <option value="bank">Bank</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700">Category</label>
                    <select class="w-full p-3 border rounded-lg" id="transactionCategory" name="category_id">
                        <!-- Categories will be populated dynamically based on transaction type -->
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700">Date</label>
                    <input type="date" id="transactionDate" name="date" class="w-full p-3 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700">Description</label>
                    <textarea id="transactionDescription" name="description" class="w-full p-3 border rounded-lg"></textarea>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" class="bg-gray-200 px-4 py-2 rounded-lg" id="closeModal">Cancel</button>
                    <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg">Add Transaction</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Category Management Modal -->
    <div id="categoryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-start justify-center z-50 overflow-y-auto">
        <div class="min-h-screen w-full flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl w-full max-w-lg relative">
                <!-- Header -->
                <div class="sticky top-0 bg-white p-4 md:p-6 border-b rounded-t-2xl z-10">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Manage Categories</h2>
                        <button type="button" class="text-gray-500 hover:text-gray-700 p-2" id="closeCategoryModal">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="p-4 md:p-6 overflow-y-auto max-h-[calc(100vh-200px)] md:max-h-[600px]">
                    <!-- Add Category Form -->
                    <form id="categoryForm" class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="col-span-1">
                                <label class="block mb-2 text-sm font-medium text-gray-700">Category Name</label>
                                <input type="text" id="categoryName" name="name" 
                                       class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-purple-500" required>
                            </div>
                            <div class="col-span-1">
                                <label class="block mb-2 text-sm font-medium text-gray-700">Type</label>
                                <select class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-purple-500" 
                                        id="categoryType" name="type">
                                    <option value="expense">Expense</option>
                                    <option value="income">Income</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" 
                                    class="w-full md:w-auto bg-purple-600 text-white px-6 py-2.5 rounded-lg hover:bg-purple-700 transition-colors duration-200">
                                Add Category
                            </button>
                        </div>
                    </form>

                    <!-- Categories List -->
                    <div class="mt-6">
                        <h3 class="font-semibold mb-3 text-lg">Existing Categories</h3>
                        <div class="bg-gray-50 rounded-lg">
                            <div class="divide-y divide-gray-200" id="categoriesList">
                                <!-- Categories will be listed here dynamically -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Transaction Modal -->
    <div id="editTransactionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Edit Transaction</h3>
                <button type="button" class="close-edit-modal text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="editTransactionForm">
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700">Type</label>
                    <select class="w-full p-3 border rounded-lg" id="editTransactionType" required>
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700">Category</label>
                    <select class="w-full p-3 border rounded-lg" id="editTransactionCategory" required>
                        <option value="">Select Category</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700">Amount</label>
                    <input type="number" class="w-full p-3 border rounded-lg" id="editTransactionAmount" required min="0" step="0.01">
                </div>
                
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700">Payment Method</label>
                    <select class="w-full p-3 border rounded-lg" id="editTransactionPaymentMethod" required>
                        <option value="cash">Cash</option>
                        <option value="bank">Bank</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700">Date</label>
                    <input type="date" class="w-full p-3 border rounded-lg" id="editTransactionDate" required>
                </div>
                
                <div class="mb-4">
                    <label class="block mb-2 text-gray-700">Description</label>
                    <textarea class="w-full p-3 border rounded-lg" id="editTransactionDescription" rows="3"></textarea>
                </div>
                
                <button type="submit" class="w-full bg-blue-500 text-white p-3 rounded-lg hover:bg-blue-600">
                    Update Transaction
                </button>
            </form>
        </div>
    </div>

    <script>
        // Add debug flag
        const DEBUG = false;

        // Debug logger function
        function debugLog(message, data = null) {
            if (DEBUG) {
                console.group(`🔍 ${message}`);
                if (data) {
                    console.log(data);
                }
                console.groupEnd();
            }
        }

        $(document).ready(function() {
            debugLog('Application initialized');

            // Initialize date picker with better configuration
            flatpickr("#dateRangePicker", {
                mode: "range",
                dateFormat: "Y-m-d",
                maxDate: "today",
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        debugLog('Date range selected:', {
                            start: selectedDates[0].toISOString().split('T')[0],
                            end: selectedDates[1].toISOString().split('T')[0]
                        });
                    }
                }
            });

            // Load initial data
            loadTransactions();
            loadCategories();
            loadSummary();

            // Transaction Modal Handlers
            $('#addTransactionBtn').click(function() {
                debugLog('Opening add transaction modal');
                // Reset form
                $('#transactionForm')[0].reset();
                // Load categories based on default type
                const defaultType = $('#transactionType').val();
                loadCategories(defaultType);
                // Show modal
                $('#transactionModal').removeClass('hidden').addClass('flex');
            });

            $('#closeModal').click(function() {
                $('#transactionModal').addClass('hidden').removeClass('flex');
            });

            // Category Modal Handlers
            $('#addCategoryBtn').click(function() {
                loadCategoriesList();
                $('#categoryModal').removeClass('hidden').addClass('flex');
            });

            $('#closeCategoryModal').click(function() {
                $('#categoryModal').addClass('hidden').removeClass('flex');
            });

            // Transaction Type Change Handler
            $('#transactionType').on('change', function() {
                const type = $(this).val();
                debugLog('Transaction type changed to:', type);
                loadCategories(type);
            });

            // Update the transaction form submission handler
            $('#transactionForm').on('submit', function(e) {
                e.preventDefault();
                console.log('Form submitted'); // Debug log

                // Create FormData object
                const formData = new FormData();
                formData.append('action', 'add_transaction');
                formData.append('type', $('#transactionType').val());
                formData.append('category_id', $('#transactionCategory').val());
                formData.append('amount', $('#transactionAmount').val());
                formData.append('date', $('#transactionDate').val());
                formData.append('description', $('#transactionDescription').val() || '');
                formData.append('payment_method', $('#transactionPaymentMethod').val());

                // Debug log the form data
                console.log('Payment Method:', $('#transactionPaymentMethod').val());
                
                $.ajax({
                    url: 'api.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        console.log('Sending data:', Object.fromEntries(formData));
                        $('#transactionForm button[type="submit"]')
                            .prop('disabled', true)
                            .html('<i class="fas fa-spinner fa-spin"></i> Adding...');
                    },
                    success: function(response) {
                        console.log('Response:', response);
                        try {
                            const result = typeof response === 'string' ? JSON.parse(response) : response;
                            if (result.success) {
                                $('#transactionModal').addClass('hidden').removeClass('flex');
                                $('#transactionForm')[0].reset();
                                loadTransactions();
                                loadSummary();
                                showAlert('Transaction added successfully', 'success');
                            } else {
                                showAlert(result.error || 'Error adding transaction', 'error');
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            showAlert('Error adding transaction', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Ajax error:', {xhr, status, error});
                        showAlert('Error adding transaction: ' + error, 'error');
                    },
                    complete: function() {
                        $('#transactionForm button[type="submit"]')
                            .prop('disabled', false)
                            .html('Add Transaction');
                    }
                });
            });

            $('#categoryForm').submit(function(e) {
                e.preventDefault();
                const formData = {
                    name: $('#categoryName').val(),
                    type: $('#categoryType').val()
                };

                $.ajax({
                    url: 'api.php?action=add_category',
                    type: 'POST',
                    data: JSON.stringify(formData),
                    contentType: 'application/json',
                    success: function(response) {
                        if (response.success) {
                            $('#categoryModal').addClass('hidden').removeClass('flex');
                            $('#categoryForm')[0].reset();
                            loadCategories();
                            showAlert('Category added successfully!', 'success');
                        }
                    },
                    error: function() {
                        showAlert('Error adding category!', 'error');
                    }
                });
            });

            // Update filter form submission handler
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();

                const dateRange = $('#dateRangePicker').val().split(' to ');
                const startDate = dateRange[0] ? dateRange[0].trim() : null;
                const endDate = dateRange[1] ? dateRange[1].trim() : null;

                const filters = {
                    start_date: startDate,
                    end_date: endDate,
                    category_id: $('#categoryFilter').val(),
                    type: $('#typeFilter').val(),
                    payment_method: $('#paymentMethodFilter').val()
                };

                debugLog('Applying filters:', filters);
                loadTransactions(filters);
            });

            // Load categories list when category modal opens
            $('#addCategoryBtn').click(function() {
                loadCategoriesList();
                $('#categoryModal').removeClass('hidden').addClass('flex');
            });

            // Reset transaction form when modal is closed
            $('#closeModal, #transactionModal').click(function(e) {
                if (e.target === this) {
                    $('#transactionForm')[0].reset();
                    $('#transactionForm').removeData('transaction-id');
                    $('#transactionForm button[type="submit"]').text('Add Transaction');
                    $('#transactionModal').addClass('hidden').removeClass('flex');
                }
            });

            // Category deletion handler
            $(document).on('click', '.delete-category', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const categoryId = $(this).data('id');
                deleteCategory(categoryId);
            });

            // Initialize date picker for edit form
            const today = new Date().toISOString().split('T')[0];
            $('#transactionDate').val(today);

            // Load initial categories based on default type (expense)
            loadCategories('expense');

            // Initialize summary
            loadSummary();

            // Refresh summary every 30 seconds
            setInterval(loadSummary, 30000);

            // Refresh summary after adding/editing transactions
            $('#transactionForm').on('submit', function() {
                setTimeout(loadSummary, 1000); // Delay to ensure transaction is saved
            });

            // Global AJAX error handler
            $(document).ajaxError(function(event, xhr, settings, error) {
                debugLog('Global AJAX error:', {
                    event: event,
                    xhr: xhr,
                    settings: settings,
                    error: error
                });
            });

            // Add edit modal to the page
            addEditModalHTML();

            // Function to populate category filter
            function populateCategoryFilter() {
                $.ajax({
                    url: 'api.php',
                    type: 'GET',
                    data: {
                        action: 'categories'
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            const categoryFilter = $('#categoryFilter');
                            categoryFilter.find('option:not(:first)').remove();

                            response.data.forEach(function(category) {
                                categoryFilter.append(`
                                    <option value="${category.category_id}">
                                        ${category.name} (${category.type})
                                    </option>
                                `);
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        debugLog('Error loading category filters:', error);
                    }
                });
            }

            // Initialize category filter
            populateCategoryFilter();

            // Update loadTransactions function to better handle date filters
            function loadTransactions(filters = {}) {
                debugLog('Loading transactions with filters:', filters);

                // Clean up filters object to remove empty values
                const cleanFilters = Object.fromEntries(
                    Object.entries(filters).filter(([_, v]) => v != null && v !== '')
                );

                $.ajax({
                    url: 'api.php',
                    type: 'GET',
                    data: {
                        action: 'transactions',
                        ...cleanFilters
                    },
                    success: function(response) {
                        debugLog('Transactions loaded:', response);
                        const tbody = $('#transactionsBody');
                        tbody.empty();

                        if (response.success && response.data) {
                            updateTransactionsTable(response.data);
                        } else {
                            $('#transactionsBody').html('<tr><td colspan="5" class="text-center p-3">No transactions found</td></tr>');
                            showAlert('No transactions found for the selected filters', 'info');
                        }
                    },
                    error: function(xhr, status, error) {
                        debugLog('Error loading transactions:', error);
                        showAlert('Error loading transactions', 'error');
                    }
                });
            }

            // Add a function to format dates consistently
            function formatDateForFilter(date) {
                if (!date) return null;
                const d = new Date(date);
                return d.toISOString().split('T')[0];
            }

            // Add a reset filters button if you want
            $('<button type="button" class="bg-gray-500 text-white px-4 py-2 rounded mobile-full-width hover:bg-gray-600 ml-2">')
                .html('<i class="fas fa-undo mr-2"></i>Reset Filters')
                .insertAfter('#filterForm button[type="submit"]')
                .click(function() {
                    $('#filterForm')[0].reset();
                    // Reset date picker
                    const fp = document.querySelector("#dateRangePicker")._flatpickr;
                    fp.setDate([new Date().setMonth(new Date().getMonth() - 1), new Date()]);
                    // Load all transactions
                    loadTransactions();
                });

            // Initialize date picker for edit form
            flatpickr("#editTransactionDate", {
                dateFormat: "Y-m-d",
                maxDate: "today"
            });

            let currentChart = null;

            // Initialize chart date range picker with a longer default range
            const defaultStartDate = new Date();
            defaultStartDate.setMonth(defaultStartDate.getMonth() - 6); // Show last 6 months by default

            flatpickr("#chartDateRange", {
                mode: "range",
                dateFormat: "Y-m-d",
                maxDate: "today",
                onChange: function(selectedDates, dateStr) {
                    debugLog('Chart date range changed:', dateStr);
                }
            });

            // Function to fetch chart data with improved error handling
            function fetchChartData() {
                debugLog('Fetching chart data...');

                const dateRange = $('#chartDateRange').val().split(' to ');
                const period = $('#chartPeriod').val();
                const dataType = $('#chartDataType').val();
                const paymentMethod = $('#chartPaymentMethod').val();

                // If no date range is selected, use default range
                const startDate = dateRange[0] || defaultStartDate.toISOString().split('T')[0];
                const endDate = dateRange[1] || new Date().toISOString().split('T')[0];

                const requestData = {
                    action: 'chart_data',
                    start_date: startDate,
                    end_date: endDate,
                    period: period,
                    data_type: dataType,
                    payment_method: paymentMethod
                };

                debugLog('Chart request data:', requestData);

                // Show loading state
                $('#financeChart').css('opacity', '0.5');

                $.ajax({
                    url: 'api.php',
                    type: 'GET',
                    data: requestData,
                    success: function(response) {
                        debugLog('Chart data response:', response);

                        // Check if we have any data
                        const hasData = response.success && response.data &&
                            ((response.data.labels && response.data.labels.length > 0) ||
                                (response.data.income && response.data.income.length > 0) ||
                                (response.data.expenses && response.data.expenses.length > 0));

                        if (hasData) {
                            updateChart(response.data);
                            updateChartStats(response.stats);
                        } else {
                            showAlert('No transactions found for the selected period', 'info');
                            // Clear the chart if no data
                            if (currentChart) {
                                currentChart.destroy();
                                currentChart = null;
                            }
                            // Clear stats
                            updateChartStats({
                                total_income: 0,
                                total_expenses: 0,
                                net_balance: 0,
                                daily_average: 0
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        debugLog('Chart data error:', {
                            xhr,
                            status,
                            error
                        });
                        showAlert('Error loading chart data: ' + error, 'error');
                    },
                    complete: function() {
                        // Restore chart opacity
                        $('#financeChart').css('opacity', '1');
                    }
                });
            }

            // Function to update chart with improved configuration
            function updateChart(data) {
                debugLog('Updating chart with data:', data);

                const ctx = document.getElementById('financeChart').getContext('2d');
                const chartType = $('#chartType').val();
                const dataType = $('#chartDataType').val();

                // First, remove any existing controls
                $('#chartControls').remove();

                // Destroy existing chart if it exists
                if (currentChart) {
                    currentChart.destroy();
                }

                // Prepare chart configuration
                const config = {
                    type: chartType,
                    data: {
                        labels: data.labels,
                        datasets: []
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            zoom: {
                                pan: {
                                    enabled: true,
                                    mode: 'xy',
                                    modifierKey: 'ctrl',
                                },
                                zoom: {
                                    wheel: {
                                        enabled: true,
                                    },
                                    pinch: {
                                        enabled: true
                                    },
                                    mode: 'xy',
                                    drag: {
                                        enabled: true,
                                        backgroundColor: 'rgba(225,225,225,0.3)',
                                        borderColor: 'rgba(225,225,225,0.3)',
                                        borderWidth: 1
                                    }
                                }
                            },
                            datalabels: {
                                display: true,
                                color: function(context) {
                                    return context.dataset.borderColor;
                                },
                                font: {
                                    weight: 'bold'
                                },
                                formatter: function(value) {
                                    return '₹' + value.toLocaleString('en-IN');
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ₹' + 
                                            context.parsed.y.toLocaleString('en-IN');
                                    }
                                }
                            },
                            legend: {
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '₹' + value.toLocaleString('en-IN');
                                    }
                                }
                            }
                        }
                    }
                };

                // Configure datasets based on data type
                if (dataType === 'category') {
                    config.data.datasets = [{
                        label: 'Amount',
                        data: data.values,
                        backgroundColor: generateColors(data.labels.length),
                        borderWidth: 1
                    }];
                } else {
                    if (dataType === 'all' || dataType === 'income') {
                        config.data.datasets.push({
                            label: 'Income',
                            data: data.income,
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.5)',
                            borderWidth: 2,
                            tension: 0.1
                        });
                    }
                    if (dataType === 'all' || dataType === 'expense') {
                        config.data.datasets.push({
                            label: 'Expenses',
                            data: data.expenses,
                            borderColor: 'rgb(255, 99, 132)',
                            backgroundColor: 'rgba(255, 99, 132, 0.5)',
                            borderWidth: 2,
                            tension: 0.1
                        });
                    }
                }

                debugLog('Creating chart with config:', config);

                // Add styled reset zoom button and controls container
                const chartControls = `
                    <div id="chartControls" class="flex flex-wrap items-center justify-between gap-4 mt-4 mb-2">
                        <div class="flex items-center gap-2">
                            <button id="resetZoom" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-all duration-300 ease-in-out flex items-center gap-2 shadow-md hover:shadow-lg">
                                <i class="fas fa-search-minus"></i>
                                Reset Zoom
                            </button>
                            <button id="toggleDataLabels" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-all duration-300 ease-in-out flex items-center gap-2 shadow-md hover:shadow-lg">
                                <i class="fas fa-tags"></i>
                                Toggle Labels
                            </button>
                        </div>
                        <div class="text-sm text-gray-600">
                            <span class="mr-4"><i class="fas fa-mouse-pointer"></i> Drag to zoom</span>
                            <span class="mr-4"><i class="fas fa-arrows-alt"></i> Ctrl + Drag to pan</span>
                            <span><i class="fas fa-search"></i> Scroll to zoom</span>
                        </div>
                    </div>
                `;
                
                $('#financeChart').before(chartControls);

                // Add button functionality
                $(document).off('click', '#resetZoom').on('click', '#resetZoom', function() {
                    if (currentChart) {
                        currentChart.resetZoom();
                    }
                });

                $(document).off('click', '#toggleDataLabels').on('click', '#toggleDataLabels', function() {
                    if (currentChart) {
                        const isVisible = currentChart.options.plugins.datalabels.display;
                        currentChart.options.plugins.datalabels.display = !isVisible;
                        
                        $(this).html(`
                            <i class="fas fa-tags"></i>
                            ${isVisible ? 'Show Labels' : 'Hide Labels'}
                        `);
                        
                        currentChart.update();
                    }
                });

                // Create new chart
                currentChart = new Chart(ctx, config);
            }

            // Function to update chart statistics with formatting
            function updateChartStats(stats) {
                debugLog('Updating chart stats:', stats);
                $('.chart-total-income').text('₹' + (stats.total_income || 0).toLocaleString());
                $('.chart-total-expenses').text('₹' + (stats.total_expenses || 0).toLocaleString());
                $('.chart-net-balance').text('₹' + (stats.net_balance || 0).toLocaleString());
                $('.chart-daily-avg').text('₹' + (stats.daily_average || 0).toLocaleString());
            }

            // Function to generate colors for pie/doughnut charts
            function generateColors(count) {
                const colors = [];
                for (let i = 0; i < count; i++) {
                    colors.push(`hsl(${(i * 360) / count}, 70%, 60%)`);
                }
                return colors;
            }

            // Event listeners for chart controls with debouncing
            let updateTimeout;

            function debouncedUpdate() {
                clearTimeout(updateTimeout);
                updateTimeout = setTimeout(fetchChartData, 300);
            }

            $('#chartType, #chartPeriod, #chartDataType, #chartPaymentMethod').on('change', debouncedUpdate);
            $('#applyChartFilters').on('click', function(e) {
                e.preventDefault();
                fetchChartData();
            });

            // Initial chart load
            fetchChartData();

            // Initialize date picker for export
            flatpickr("#exportDateRange", {
                mode: "range",
                dateFormat: "Y-m-d",
                maxDate: "today",
                defaultDate: [new Date().setMonth(new Date().getMonth() - 1), new Date()]
            });

            // Handle export form submission
            $('#exportForm').on('submit', function(e) {
                e.preventDefault();

                const dateRange = $('#exportDateRange').val().split(' to ');
                const exportType = $('#exportType').val();
                const exportFormat = $('#exportFormat').val();

                // Create form data
                const formData = new FormData();
                formData.append('action', 'export_data');
                formData.append('type', exportType);
                formData.append('format', exportFormat);
                formData.append('start_date', dateRange[0] || '');
                formData.append('end_date', dateRange[1] || '');

                // Show loading state
                const submitButton = $(this).find('button[type="submit"]');
                const originalText = submitButton.html();
                submitButton.prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin mr-2"></i>Exporting...');

                // Make AJAX request
                $.ajax({
                    url: 'api.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhrFields: {
                        responseType: 'blob' // Important for handling file download
                    },
                    success: function(response, status, xhr) {
                        // Get filename from response header
                        const filename = xhr.getResponseHeader('Content-Disposition')?.split('filename=')[1] ||
                            `finance_export_${new Date().toISOString().split('T')[0]}.${exportFormat}`;

                        // Create download link
                        const url = window.URL.createObjectURL(new Blob([response]));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', filename);
                        document.body.appendChild(link);
                        link.click();
                        link.remove();
                        window.URL.revokeObjectURL(url);

                        showAlert('Export completed successfully', 'success');
                    },
                    error: function(xhr) {
                        let errorMessage = 'Export failed';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMessage = response.error || errorMessage;
                        } catch (e) {
                            console.error('Export error:', e);
                        }
                        showAlert(errorMessage, 'error');
                    },
                    complete: function() {
                        submitButton.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Use event delegation for edit and delete buttons since the table rows are dynamically added
            $(document).on('click', '.edit-transaction', function() {
                const transactionId = $(this).data('id');
                debugLog('Edit button clicked for transaction:', transactionId);
                editTransaction(transactionId);
            });

            $(document).on('click', '.delete-transaction', function() {
                const transactionId = $(this).data('id');
                debugLog('Delete button clicked for transaction:', transactionId);
                deleteTransaction(transactionId);
            });

            // Update the updateTransactionsTable function to ensure buttons have proper classes and data attributes
            function updateTransactionsTable(transactions) {
                debugLog('Updating transactions table', transactions);
                const tbody = $('#transactionsBody');
                tbody.empty();

                transactions.forEach(transaction => {
                    const row = `
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3">${formatDate(transaction.transaction_date)}</td>
                            <td class="p-3">${transaction.category_name}</td>
                            <td class="p-3">
                                <span class="px-2 py-1 rounded-full text-xs ${
                                    transaction.type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                }">
                                    ${transaction.type.charAt(0).toUpperCase() + transaction.type.slice(1)}
                                </span>
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-1 rounded-full text-xs ${
                                    transaction.payment_method === 'cash' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'
                                }">
                                    ${transaction.payment_method.charAt(0).toUpperCase() + transaction.payment_method.slice(1)}
                                </span>
                            </td>
                            <td class="p-3 text-right">₹${parseFloat(transaction.amount).toFixed(2)}</td>
                            <td class="p-3 text-center">
                                <button type="button" 
                                    class="edit-transaction text-blue-600 hover:text-blue-800 mx-1" 
                                    data-id="${transaction.transaction_id}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" 
                                    class="delete-transaction text-red-600 hover:text-red-800 mx-1" 
                                    data-id="${transaction.transaction_id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            }

            // Update the editTransaction function to properly show the modal
            function editTransaction(transactionId) {
                console.log('Editing transaction:', transactionId);
                
                $.ajax({
                    url: 'api.php',
                    type: 'GET',
                    data: {
                        action: 'get_transaction',
                        id: transactionId
                    },
                    success: function(response) {
                        console.log('Get Transaction Response:', response);
                        
                        if (response.success && response.data) {
                            const transaction = response.data;
                            
                            // Set transaction ID in the form
                            $('#editTransactionForm').data('transaction-id', transactionId);
                            
                            // Populate form fields
                            $('#editTransactionType').val(transaction.type);
                            $('#editTransactionAmount').val(transaction.amount);
                            $('#editTransactionPaymentMethod').val(transaction.payment_method);
                            $('#editTransactionDate').val(transaction.transaction_date);
                            $('#editTransactionDescription').val(transaction.description || '');
                            
                            // Load categories for the transaction type
                            $.ajax({
                                url: 'api.php',
                                type: 'GET',
                                data: {
                                    action: 'categories',
                                    type: transaction.type
                                },
                                success: function(categoryResponse) {
                                    if (categoryResponse.success && categoryResponse.data) {
                                        const categorySelect = $('#editTransactionCategory');
                                        categorySelect.empty();
                                        categorySelect.append('<option value="">Select Category</option>');
                                        
                                        categoryResponse.data.forEach(category => {
                                            categorySelect.append(`
                                                <option value="${category.category_id}" 
                                                    ${category.category_id == transaction.category_id ? 'selected' : ''}>
                                                    ${category.name}
                                                </option>
                                            `);
                                        });
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error('Category Load Error:', error);
                                    showAlert('Error loading categories', 'error');
                                }
                            });
                            
                            // Show the modal
                            $('#editTransactionModal').removeClass('hidden').addClass('flex');
                        } else {
                            showAlert(response.error || 'Error loading transaction details', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Edit Transaction Error:', error);
                        showAlert('Error loading transaction details', 'error');
                    }
                });
            }

            // Update the deleteTransaction function to use proper confirmation
            function deleteTransaction(transactionId) {
                if (!confirm('Are you sure you want to delete this transaction?')) {
                    return;
                }

                debugLog('Deleting transaction:', transactionId);

                $.ajax({
                    url: 'api.php',
                    type: 'POST',
                    data: {
                        action: 'delete_transaction',
                        transaction_id: transactionId
                    },
                    success: function(response) {
                        debugLog('Delete response:', response);
                        if (response.success) {
                            showAlert('Transaction deleted successfully', 'success');
                            loadTransactions();
                            loadSummary();
                        } else {
                            showAlert(response.error || 'Failed to delete transaction', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        debugLog('Delete error:', { xhr, status, error });
                        showAlert('Error deleting transaction', 'error');
                    }
                });
            }

            // Back button functionality
            $('.back-button').click(function() {
                window.location.href = '../';
            });

            // Edit Transaction Form Handler
            $('#editTransactionForm').on('submit', function(e) {
                e.preventDefault();
                
                const transactionId = $(this).data('transaction-id');
                const formData = new FormData();
                
                // Append all form data
                formData.append('action', 'update_transaction');
                formData.append('transaction_id', transactionId);
                formData.append('type', $('#editTransactionType').val());
                formData.append('category_id', $('#editTransactionCategory').val());
                formData.append('amount', $('#editTransactionAmount').val());
                formData.append('date', $('#editTransactionDate').val());
                formData.append('description', $('#editTransactionDescription').val() || '');
                formData.append('payment_method', $('#editTransactionPaymentMethod').val());

                // Debug log
                console.log('Edit Transaction ID:', transactionId);
                console.log('Form Data:', Object.fromEntries(formData));

                $.ajax({
                    url: 'api.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('#editTransactionForm button[type="submit"]')
                            .prop('disabled', true)
                            .html('<i class="fas fa-spinner fa-spin"></i> Updating...');
                    },
                    success: function(response) {
                        console.log('Update Response:', response);
                        if (response.success) {
                            $('#editTransactionModal').addClass('hidden');
                            $('#editTransactionForm')[0].reset();
                            loadTransactions();
                            loadSummary();
                            showAlert('Transaction updated successfully', 'success');
                        } else {
                            showAlert(response.error || 'Failed to update transaction', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Update Error:', {xhr, status, error});
                        showAlert('Error updating transaction: ' + error, 'error');
                    },
                    complete: function() {
                        $('#editTransactionForm button[type="submit"]')
                            .prop('disabled', false)
                            .html('Update Transaction');
                    }
                });
            });

            // Edit Transaction Function
            function editTransaction(transactionId) {
                console.log('Editing transaction:', transactionId);
                
                $.ajax({
                    url: 'api.php',
                    type: 'GET',
                    data: {
                        action: 'get_transaction',
                        id: transactionId
                    },
                    success: function(response) {
                        console.log('Get Transaction Response:', response);
                        
                        if (response.success && response.data) {
                            const transaction = response.data;
                            
                            // Set transaction ID in the form
                            $('#editTransactionForm').data('transaction-id', transactionId);
                            
                            // Populate form fields
                            $('#editTransactionType').val(transaction.type);
                            $('#editTransactionAmount').val(transaction.amount);
                            $('#editTransactionPaymentMethod').val(transaction.payment_method);
                            $('#editTransactionDate').val(transaction.transaction_date);
                            $('#editTransactionDescription').val(transaction.description || '');
                            
                            // Load categories for the transaction type
                            $.ajax({
                                url: 'api.php',
                                type: 'GET',
                                data: {
                                    action: 'categories',
                                    type: transaction.type
                                },
                                success: function(categoryResponse) {
                                    if (categoryResponse.success && categoryResponse.data) {
                                        const categorySelect = $('#editTransactionCategory');
                                        categorySelect.empty();
                                        categorySelect.append('<option value="">Select Category</option>');
                                        
                                        categoryResponse.data.forEach(category => {
                                            categorySelect.append(`
                                                <option value="${category.category_id}" 
                                                    ${category.category_id == transaction.category_id ? 'selected' : ''}>
                                                    ${category.name}
                                                </option>
                                            `);
                                        });
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error('Category Load Error:', error);
                                    showAlert('Error loading categories', 'error');
                                }
                            });
                            
                            // Show the modal
                            $('#editTransactionModal').removeClass('hidden').addClass('flex');
                        } else {
                            showAlert(response.error || 'Error loading transaction details', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Edit Transaction Error:', error);
                        showAlert('Error loading transaction details', 'error');
                    }
                });
            }

            // Add change handler for edit transaction type
            $('#editTransactionType').on('change', function() {
                const selectedType = $(this).val();
                
                // Load categories for selected type
                $.ajax({
                    url: 'api.php',
                    type: 'GET',
                    data: {
                        action: 'categories',
                        type: selectedType
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            const categorySelect = $('#editTransactionCategory');
                            categorySelect.empty();
                            categorySelect.append('<option value="">Select Category</option>');
                            
                            response.data.forEach(category => {
                                categorySelect.append(`
                                    <option value="${category.category_id}">
                                        ${category.name}
                                    </option>
                                `);
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Category Load Error:', error);
                        showAlert('Error loading categories', 'error');
                    }
                });
            });

            // Close edit modal handler
            $('#closeEditModal').on('click', function() {
                $('#editTransactionModal').addClass('hidden').removeClass('flex');
            });

            // Initialize event handlers when document is ready
            $('#editTransactionType').on('change', function() {
                loadEditCategories($(this).val());
            });

            $('.close-edit-modal').on('click', function() {
                $('#editTransactionModal').addClass('hidden').removeClass('flex');
            });

            // Initialize edit form handler
            $('#editTransactionForm').on('submit', function(e) {
                e.preventDefault();
                const transactionId = $(this).data('transaction-id');
                submitEditTransaction(transactionId);
            });
        });

        // Helper Functions
        function loadTransactions() {
            debugLog('Loading transactions');

            $.ajax({
                url: 'api.php',
                type: 'GET',
                data: {
                    action: 'transactions',
                    // Add any filters here
                },
                success: function(response) {
                    debugLog('Transactions loaded:', response);
                    const tbody = $('#transactionsBody');
                    tbody.empty();

                    if (response.success && response.data) {
                        response.data.forEach(function(transaction) {
                            tbody.append(`
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-3">${formatDate(transaction.transaction_date)}</td>
                                    <td class="p-3">${transaction.category_name}</td>
                                    <td class="p-3">
                                        <span class="px-2 py-1 rounded-full text-xs ${
                                            transaction.type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                        }">
                                            ${transaction.type.charAt(0).toUpperCase() + transaction.type.slice(1)}
                                        </span>
                                    </td>
                                    <td class="p-3">
                                        <span class="px-2 py-1 rounded-full text-xs ${
                                            transaction.payment_method === 'cash' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'
                                        }">
                                            ${transaction.payment_method.charAt(0).toUpperCase() + transaction.payment_method.slice(1)}
                                        </span>
                                    </td>
                                    <td class="p-3 text-right">₹${parseFloat(transaction.amount).toFixed(2)}</td>
                                    <td class="p-3 text-center">
                                        <button type="button" 
                                            class="edit-transaction text-blue-600 hover:text-blue-800 mx-1" 
                                            data-id="${transaction.transaction_id}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" 
                                            class="delete-transaction text-red-600 hover:text-red-800 mx-1" 
                                            data-id="${transaction.transaction_id}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `);
                        });
                    } else {
                        tbody.html('<tr><td colspan="5" class="text-center p-3">No transactions found</td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    debugLog('Error loading transactions:', {
                        xhr,
                        status,
                        error
                    });
                    showAlert('Error loading transactions', 'error');
                }
            });
        }

        function loadCategories(type = null) {
            debugLog('Loading categories for type:', type);

            const categorySelect = $('#transactionCategory');
            categorySelect.prop('disabled', true);

            $.ajax({
                url: 'api.php',
                type: 'GET',
                data: {
                    action: 'categories',
                    type: type
                },
                success: function(response) {
                    debugLog('Categories loaded:', response);
                    categorySelect.empty();
                    categorySelect.append('<option value="">Select Category</option>');

                    if (response.success && response.data) {
                        response.data
                            .filter(category => !type || category.type === type)
                            .forEach(category => {
                                categorySelect.append(`
                                    <option value="${category.category_id}">
                                        ${category.name}
                                    </option>
                                `);
                            });
                    }
                    categorySelect.prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    debugLog('Error loading categories:', {
                        xhr,
                        status,
                        error
                    });
                    showAlert('Error loading categories', 'error');
                    categorySelect.prop('disabled', false);
                }
            });
        }

        function loadSummary() {
            debugLog('Loading summary');

            // First load the regular summary data
            $.ajax({
                url: 'api.php',
                type: 'GET',
                data: {
                    action: 'summary'
                },
                beforeSend: function() {
                    // Add loading state
                    $('.total-income, .total-expense, .net-balance').text('Loading...');
                    $('.income-change, .expense-change').html('<span class="text-gray-400">...</span>');
                },
                success: function(response) {
                    debugLog('Summary loaded', response);
                    if (response.success) {
                        // Load budgets data
                        $.ajax({
                            url: 'api.php',
                            type: 'GET',
                            data: {
                                action: 'get_budgets'
                            },
                            success: function(budgetResponse) {
                                debugLog('Budgets loaded', budgetResponse);
                                if (budgetResponse.success) {
                                    // Combine summary and budget data
                                    response.data.budgets = budgetResponse.data;
                                    updateSummaryDisplay(response.data);
                                } else {
                                    showAlert('Error loading budgets: ' + budgetResponse.error, 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                debugLog('Error loading budgets', {
                                    status: status,
                                    error: error,
                                    response: xhr.responseText
                                });
                                showAlert('Error loading budgets', 'error');
                            }
                        });
                    } else {
                        showAlert('Error loading summary: ' + response.error, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    debugLog('Error loading summary', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                    showAlert('Error loading summary', 'error');
                }
            });
        }

        function updateSummaryDisplay(summary) {
            debugLog('Updating summary display', summary);

            // Format currency
            const formatCurrency = (amount) => {
                return '₹' + parseFloat(amount).toLocaleString('en-IN', {
                    maximumFractionDigits: 2,
                    minimumFractionDigits: 0
                });
            };

            // Format percentage change
            const formatChange = (change, isIncome = true) => {
                const absChange = Math.abs(change);
                const direction = change >= 0 ? '↑' : '↓';
                const colorClass = isIncome ?
                    (change >= 0 ? 'text-green-500' : 'text-red-500') :
                    (change >= 0 ? 'text-red-500' : 'text-green-500');

                return `<span class="${colorClass}">${direction} ${absChange}%</span>`;
            };

            // Clear existing content
            $('#summarySection').empty();

            // Add current month summary
            const currentMonthSummaryHtml = `
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Current Month (${summary.current_month_name})</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white rounded-lg shadow p-4">
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Monthly Income</h3>
                            <p class="text-2xl font-bold text-green-600">${formatCurrency(summary.current_month.total_income)}</p>
                            <p class="text-sm text-gray-500">vs Last Month ${formatChange(summary.current_month.income_change, true)}</p>
                        </div>
                        <div class="bg-white rounded-lg shadow p-4">
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Monthly Expenses</h3>
                            <p class="text-2xl font-bold text-red-600">${formatCurrency(summary.current_month.total_expense)}</p>
                            <p class="text-sm text-gray-500">vs Last Month ${formatChange(summary.current_month.expense_change, false)}</p>
                        </div>
                        <div class="bg-white rounded-lg shadow p-4">
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Monthly Balance</h3>
                            <p class="text-2xl font-bold text-blue-600">
                                ${formatCurrency(summary.current_month.net_balance)}
                            </p>
                        </div>
                    </div>
                </div>
            `;

            // Add cumulative summary
            const cumulativeSummaryHtml = `
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Year to Date (${summary.cumulative.from_date} - ${summary.cumulative.to_date})</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Income</h3>
                            <p class="text-2xl font-bold text-green-600">${formatCurrency(summary.cumulative.total_income)}</p>
                        </div>
                        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Expenses</h3>
                            <p class="text-2xl font-bold text-red-600">${formatCurrency(summary.cumulative.total_expense)}</p>
                        </div>
                        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Net Profit</h3>
                            <p class="text-2xl font-bold text-blue-600">${formatCurrency(summary.cumulative.net_profit)}</p>
                        </div>
                    </div>
                </div>
            `;

            // Add payment methods section
            const paymentMethodsHtml = `
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Payment Methods</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white rounded-lg shadow p-4">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Current Month</h3>
                            <div class="grid grid-cols-1 gap-4">
                                ${Object.entries(summary.current_month.payment_methods).map(([method, data]) => `
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <div class="flex items-center mb-3">
                                            <i class="fas fa-${method === 'cash' ? 'money-bill-alt' : 'university'} text-gray-600 mr-2"></i>
                                            <h3 class="text-lg font-semibold capitalize">${method}</h3>
                                        </div>
                                        <div class="grid grid-cols-3 gap-4">
                                            <div>
                                                <p class="text-sm text-gray-600 mb-1">Income</p>
                                                <p class="text-base font-medium text-green-600">${formatCurrency(data.income)}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600 mb-1">Expense</p>
                                                <p class="text-base font-medium text-red-600">${formatCurrency(data.expense)}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600 mb-1">Balance</p>
                                                <p class="text-base font-medium text-blue-600">${formatCurrency(data.balance)}</p>
                                            </div>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow p-4">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Year to Date</h3>
                            <div class="grid grid-cols-1 gap-4">
                                ${Object.entries(summary.cumulative.payment_methods).map(([method, data]) => `
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <div class="flex items-center mb-3">
                                            <i class="fas fa-${method === 'cash' ? 'money-bill-alt' : 'university'} text-gray-600 mr-2"></i>
                                            <h3 class="text-lg font-semibold capitalize">${method}</h3>
                                        </div>
                                        <div class="grid grid-cols-3 gap-4">
                                            <div>
                                                <p class="text-sm text-gray-600 mb-1">Income</p>
                                                <p class="text-base font-medium text-green-600">${formatCurrency(data.income)}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600 mb-1">Expense</p>
                                                <p class="text-base font-medium text-red-600">${formatCurrency(data.expense)}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600 mb-1">Balance</p>
                                                <p class="text-base font-medium text-blue-600">${formatCurrency(data.balance)}</p>
                                            </div>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Add budget tracking section
            const budgetTrackingHtml = `
                <div class="mb-6 bg-white rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Budget Tracking</h2>
                            <p class="text-sm text-gray-600 mt-1">Monitor your spending against set budgets</p>
                        </div>
                        <button onclick="openBudgetModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
                            <i class="fas fa-plus mr-2"></i>Set Budget
                        </button>
                    </div>

                    ${summary.budgets && Object.keys(summary.budgets).length > 0 ? `
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            ${Object.entries(summary.budgets).map(([category, budget]) => {
                                const status = budget.percentage >= 90 ? 'critical' :
                                             budget.percentage >= 75 ? 'warning' : 'good';
                                const statusColors = {
                                    critical: 'border-red-500 bg-red-50',
                                    warning: 'border-yellow-500 bg-yellow-50',
                                    good: 'border-green-500 bg-green-50'
                                };
                                return `
                                    <div class="rounded-lg border-l-4 p-4 ${statusColors[status]} transition-all duration-300 hover:shadow-md">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-800">${category}</h3>
                                                <span class="inline-block px-2 py-1 text-xs rounded-full ${
                                                    budget.period === 'monthly' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'
                                                } mt-1">${budget.period}</span>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-medium text-gray-600">Budget</p>
                                                <p class="text-lg font-bold ${getBudgetTextColor(budget.percentage)}">${formatCurrency(budget.amount)}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="space-y-2">
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">Spent</span>
                                                <span class="font-medium">${formatCurrency(budget.spent)}</span>
                                            </div>
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">Remaining</span>
                                                <span class="font-medium ${budget.remaining > 0 ? 'text-green-600' : 'text-red-600'}">
                                                    ${formatCurrency(budget.remaining)}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="text-sm font-medium ${getBudgetTextColor(budget.percentage)}">
                                                    ${budget.percentage}% used
                                                </span>
                                                <span class="text-xs text-gray-500">
                                                    ${budget.status === 'exceeded' ? 'Budget Exceeded' :
                                                      budget.status === 'critical' ? 'Critical Level' :
                                                      budget.status === 'warning' ? 'Warning Level' : 'On Track'}
                                                </span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                                <div class="h-full rounded-full ${getBudgetColor(budget.percentage)} transition-all duration-300" 
                                                     style="width: ${Math.min(budget.percentage, 100)}%">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-3 pt-3 border-t border-gray-200">
                                            <div class="flex justify-between items-center text-xs text-gray-500">
                                                <span>Started: ${new Date(budget.start_date).toLocaleDateString()}</span>
                                                <div class="flex items-center space-x-2 gap-2">
                                                <button onclick="editBudget('${category}', ${JSON.stringify(budget).replace(/"/g, '&quot;')})" 
                                                        class="text-blue-600 hover:text-blue-800">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <button onclick="deleteBudget('${budget.category_id}', '${category}')"
                                                            data-delete-budget="${budget.category_id}"
                                                            class="text-red-600 hover:text-red-800 transition-colors">
                                                        <i class="fas fa-trash"></i> Delete
                                                </button>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg overflow-auto">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                        <span class="text-sm text-gray-600">On Track</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                                        <span class="text-sm text-gray-600">Warning (≥75%)</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 bg-red-600 rounded-full mr-2"></div>
                                        <span class="text-sm text-gray-600">Critical (≥90%)</span>
                                    </div>
                                </div>
                                <button onclick="refreshBudgets()" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-sync-alt mr-1"></i> Refresh
                                </button>
                            </div>
                        </div>
                    ` : `
                        <div class="text-center py-8 bg-gray-50 rounded-lg">
                            <div class="mb-4">
                                <i class="fas fa-chart-pie text-4xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">No Budgets Set</h3>
                            <p class="text-gray-600 mb-4">Start tracking your expenses by setting up category budgets</p>
                            <button onclick="openBudgetModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg inline-flex items-center">
                                <i class="fas fa-plus mr-2"></i>Set Your First Budget
                            </button>
                        </div>
                    `}
                </div>
            `;

            // Add the refreshBudgets function
            function refreshBudgets() {
                loadSummary();
                showAlert('Refreshing budgets...', 'success');
            }

            // Add the editBudget function
            function editBudget(category, budget) {
                openBudgetModal();
                // Pre-fill the form with existing budget data
                setTimeout(() => {
                    $('#budgetCategory').val(category);
                    $('#budgetAmount').val(budget.amount);
                    $('#budgetPeriod').val(budget.period);
                }, 100);
            }

            // Combine all sections in the desired order
            $('#summarySection').html(currentMonthSummaryHtml + cumulativeSummaryHtml + paymentMethodsHtml + budgetTrackingHtml);

            debugLog('Summary display updated');
        }

        // Helper function for budget color coding
        function getBudgetColor(percentage) {
            if (percentage >= 90) return 'bg-red-600';
            if (percentage >= 75) return 'bg-yellow-500';
            return 'bg-green-500';
        }

        function getBudgetTextColor(percentage) {
            if (percentage >= 90) return 'text-red-600';
            if (percentage >= 75) return 'text-yellow-600';
            return 'text-green-600';
        }

        // Budget Modal Functions
        function openBudgetModal() {
            const modalHtml = `
                <div id="budgetModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                    <div class="relative top-20 mx-auto p-6 border w-[480px] shadow-xl rounded-lg bg-white" style="max-width: 80%;">
                        <!-- Close button -->
                        <button onclick="closeBudgetModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>

                        <div class="mb-6">
                            <div class="flex items-center mb-1">
                                <i class="fas fa-wallet text-blue-500 text-2xl mr-3"></i>
                                <h3 class="text-xl font-semibold text-gray-900">Set Budget</h3>
                            </div>
                            <p class="text-sm text-gray-600">Create a budget to track your expenses by category</p>
                        </div>

                        <form id="budgetForm" class="space-y-6">
                            <!-- Category Selection -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    Expense Category <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select id="budgetCategory" 
                                            class="appearance-none block w-full px-4 py-3 rounded-lg border border-gray-300 
                                                   bg-white shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 
                                                   transition-all">
                                        <option value="" disabled selected>Select a category</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Amount Input -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    Budget Amount <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                        <span class="text-gray-500">₹</span>
                                    </div>
                                    <input type="number" 
                                           id="budgetAmount" 
                                           placeholder="Enter amount"
                                           class="block w-full pl-8 pr-4 py-3 rounded-lg border border-gray-300 
                                                  shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 
                                                  transition-all" 
                                           required>
                                </div>
                                <p class="text-xs text-gray-500">Set a realistic budget amount for better tracking</p>
                            </div>

                            <!-- Period Selection -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    Budget Period <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-2 gap-4">
                                    <label class="relative flex items-center justify-center p-3 rounded-lg border 
                                                 cursor-pointer hover:bg-gray-50 transition-all">
                                        <input type="radio" name="budgetPeriod" value="monthly" 
                                               class="absolute h-0 w-0 opacity-0" checked>
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-calendar-alt text-gray-400"></i>
                                            <span class="font-medium text-gray-700">Monthly</span>
                                        </div>
                                    </label>
                                    <label class="relative flex items-center justify-center p-3 rounded-lg border 
                                                 cursor-pointer hover:bg-gray-50 transition-all">
                                        <input type="radio" name="budgetPeriod" value="yearly" 
                                               class="absolute h-0 w-0 opacity-0">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-calendar text-gray-400"></i>
                                            <span class="font-medium text-gray-700">Yearly</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                                <button type="button" 
                                        onclick="closeBudgetModal()"
                                        class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 
                                               focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all">
                                    Cancel
                                </button>
                                <button type="submit" 
                                        class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 
                                               focus:outline-none focus:ring-2 focus:ring-blue-300 transition-all
                                               flex items-center">
                                    <i class="fas fa-check mr-2"></i>
                                    Set Budget
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            `;

            // Add modal to body
            $('body').append(modalHtml);

            // Add active states for period selection
            $('input[name="budgetPeriod"]').change(function() {
                // Remove active state from all labels
                $('input[name="budgetPeriod"]').parent().removeClass('border-blue-500 bg-blue-50');
                // Add active state to selected label
                $(this).parent().addClass('border-blue-500 bg-blue-50');
            });

            // Trigger change event for default selection
            $('input[name="budgetPeriod"]:checked').trigger('change');

            // Load expense categories
            loadExpenseCategories();

            // Handle form submission
            $('#budgetForm').on('submit', function(e) {
                e.preventDefault();
                setBudget();
            });
        }

        function closeBudgetModal() {
            $('#budgetModal').remove();
        }

        function loadExpenseCategories() {
            // Fetch expense categories and populate the dropdown
            $.ajax({
                url: 'api.php',
                type: 'GET',
                data: {
                    action: 'categories',
                    type: 'expense'
                },
                success: function(response) {
                    if (response.success) {
                        const select = $('#budgetCategory');
                        select.empty();
                        select.append(new Option('Select Category', '', true, true));
                        
                        response.data.forEach(category => {
                            select.append(new Option(category.name, category.category_id));
                        });
                    } else {
                        showAlert('Error loading categories: ' + response.error, 'error');
                    }
                },
                error: function() {
                    showAlert('Failed to load categories', 'error');
                }
            });
        }

        function setBudget() {
            const categoryId = $('#budgetCategory').val();
            const amount = $('#budgetAmount').val();
            const period = $('input[name="budgetPeriod"]:checked').val();
            const isEdit = $('#budgetForm').data('editing-category') !== undefined;

            if (!categoryId || !amount || !period) {
                showAlert('Please fill in all required fields', 'error');
                return;
            }

            const budgetData = {
                category_id: categoryId,
                amount: amount,
                period: period,
                is_update: isEdit // Add flag to indicate if this is an update
            };

            // Show loading state
            const submitButton = $('#budgetForm button[type="submit"]');
            const originalText = submitButton.html();
            submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Saving...');

            $.ajax({
                url: 'api.php?action=set_budget',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(budgetData),
                success: function(response) {
                    if (response.success) {
                        closeBudgetModal();
                        loadSummary(); // Refresh the budget display
                        showAlert(isEdit ? 'Budget updated successfully' : 'Budget saved successfully', 'success');
                    } else {
                        showAlert(response.error || 'Error saving budget', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    showAlert('Error saving budget: ' + error, 'error');
                    console.error('Budget save error:', error);
                },
                complete: function() {
                    // Restore button state
                    submitButton.prop('disabled', false).html(originalText);
                }
            });
        }

        function editBudget(category, budgetData) {
            // Open the budget modal
            openBudgetModal();

            // Wait for the modal and categories to load
            setTimeout(() => {
                // Find the category option and select it
                const categorySelect = $('#budgetCategory');
                categorySelect.find('option').each(function() {
                    if ($(this).text() === category) {
                        $(this).prop('selected', true);
                    }
                });

                // Set the amount
                $('#budgetAmount').val(budgetData.amount);

                // Set the period using radio buttons
                $(`input[name="budgetPeriod"][value="${budgetData.period}"]`).prop('checked', true).trigger('change');

                // Update modal title and button text to reflect edit mode
                $('#budgetModal h3').text('Edit Budget');
                $('#budgetForm button[type="submit"]').html('<i class="fas fa-save mr-2"></i>Update Budget');

                // Store the original category for reference
                $('#budgetForm').data('editing-category', category);
            }, 300); // Wait for modal to fully load
        }

        function showAlert(message, type = 'success') {
            const alertDiv = $(`<div class="fixed top-4 right-4 p-4 rounded-lg ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            } text-white shadow-lg z-50"></div>`);

            alertDiv.text(message);
            $('body').append(alertDiv);

            setTimeout(() => {
                alertDiv.fadeOut(500, function() {
                    $(this).remove();
                });
            }, 3000);
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-IN', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        // Function to delete category
        function deleteCategory(categoryId) {
            debugLog('Attempting to delete category:', categoryId);

            if (!confirm('Are you sure you want to delete this category? This cannot be undone.')) {
                return;
            }

            $.ajax({
                url: 'api.php?action=delete_category', // Fixed: Added action as URL parameter
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    id: categoryId // Fixed: Only send the ID in the data
                }),
                beforeSend: function() {
                    debugLog('Sending delete request for category:', categoryId);
                },
                success: function(response) {
                    debugLog('Delete category response:', response);
                    if (response.success) {
                        showAlert('Category deleted successfully', 'success');
                        loadCategoriesList(); // Refresh the categories list
                        loadCategories(); // Refresh categories in transaction form
                    } else {
                        showAlert(response.error || 'Failed to delete category', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    debugLog('Error deleting category:', {
                        status: status,
                        error: error,
                        response: xhr.responseText,
                        xhr: xhr
                    });

                    let errorMessage = 'Failed to delete category';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMessage = response.error || errorMessage;
                    } catch (e) {
                        debugLog('Error parsing error response:', e);
                    }

                    showAlert(errorMessage, 'error');
                }
            });
        }

        // Update loadCategoriesList function to properly bind delete buttons
        function loadCategoriesList() {
            debugLog('Loading categories list');

            $.ajax({
                url: 'api.php',
                type: 'GET',
                data: { action: 'categories' },
                success: function(response) {
                    debugLog('Categories loaded', response);
                    const categoriesList = $('#categoriesList');
                    categoriesList.empty();

                    if (!response.success) {
                        categoriesList.html('<div class="text-center text-red-500 p-4">Error loading categories</div>');
                        return;
                    }

                    if (!response.data || response.data.length === 0) {
                        categoriesList.html('<div class="text-center text-gray-500 p-4">No categories found</div>');
                        return;
                    }

                    response.data.forEach(function(category) {
                        const isDefault = category.is_default == 1;
                        const hasTransactions = category.transaction_count > 0;

                        categoriesList.append(`
                            <div class="p-4 hover:bg-white transition-colors duration-200">
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-2">
                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center gap-2 mb-1">
                                            <span class="font-medium text-gray-900">${category.name}</span>
                                            <span class="px-2 py-0.5 text-sm rounded-full ${
                                                category.type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                            }">${category.type}</span>
                                            ${isDefault ? 
                                                '<span class="px-2 py-0.5 text-xs bg-blue-100 text-blue-800 rounded-full">Default</span>' 
                                                : ''}
                                        </div>
                                        ${hasTransactions ? 
                                            `<span class="text-sm text-gray-500">${category.transaction_count} transaction(s)</span>` 
                                            : ''}
                                    </div>
                                    ${!isDefault ? `
                                        <button type="button" 
                                            class="delete-category-btn inline-flex items-center justify-center w-8 h-8 rounded-full text-red-500 hover:bg-red-50 hover:text-red-700 transition-colors duration-200 ${hasTransactions ? 'opacity-50 cursor-not-allowed' : ''}" 
                                            data-category-id="${category.category_id}"
                                            ${hasTransactions ? 'disabled title="Cannot delete category with transactions"' : 'title="Delete category"'}>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    ` : ''}
                                </div>
                            </div>
                        `);
                    });

                    // Bind click events to delete buttons
                    $('.delete-category-btn').on('click', function() {
                        const categoryId = $(this).data('category-id');
                        if (categoryId) {
                            deleteCategory(categoryId);
                        }
                    });
                },
                error: function(xhr, status, error) {
                    debugLog('Error loading categories:', { status, error, response: xhr.responseText });
                    $('#categoriesList').html('<div class="text-center text-red-500 p-4">Failed to load categories</div>');
                    showAlert('Error loading categories', 'error');
                }
            });
        }

        // Function to add edit modal HTML
        function addEditModalHTML() {
            const editModalHTML = `
            <div id="editTransactionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full mx-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Edit Transaction</h3>
                        <button type="button" class="close-edit-modal text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <form id="editTransactionForm">
                        <div class="mb-4">
                            <label class="block mb-2 text-gray-700">Type</label>
                            <select class="w-full p-3 border rounded-lg" id="editTransactionType" required>
                                <option value="income">Income</option>
                                <option value="expense">Expense</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block mb-2 text-gray-700">Category</label>
                            <select class="w-full p-3 border rounded-lg" id="editTransactionCategory" required>
                                <option value="">Select Category</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block mb-2 text-gray-700">Amount</label>
                            <input type="number" class="w-full p-3 border rounded-lg" id="editTransactionAmount" required min="0" step="0.01">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block mb-2 text-gray-700">Payment Method</label>
                            <select class="w-full p-3 border rounded-lg" id="editTransactionPaymentMethod" required>
                                <option value="cash">Cash</option>
                                <option value="bank">Bank</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block mb-2 text-gray-700">Date</label>
                            <input type="date" class="w-full p-3 border rounded-lg" id="editTransactionDate" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block mb-2 text-gray-700">Description</label>
                            <textarea class="w-full p-3 border rounded-lg" id="editTransactionDescription" rows="3"></textarea>
                        </div>
                        
                        <button type="submit" class="w-full bg-blue-500 text-white p-3 rounded-lg hover:bg-blue-600">
                            Update Transaction
                        </button>
                    </form>
                </div>
            </div>`;

            $('body').append(editModalHTML);
        }

        // Function to load categories for edit modal
        function loadEditCategories(type, selectedCategoryId) {
            debugLog('Loading categories for edit modal:', {
                type,
                selectedCategoryId
            });

            const categorySelect = $('#editTransactionCategory');
            categorySelect.prop('disabled', true);

            $.ajax({
                url: 'api.php',
                type: 'GET',
                data: {
                    action: 'categories',
                    type: type
                },
                success: function(response) {
                    debugLog('Categories loaded for edit:', response);
                    categorySelect.empty();
                    categorySelect.append('<option value="">Select Category</option>');

                    if (response.success && response.data) {
                        response.data
                            .filter(category => !type || category.type === type)
                            .forEach(category => {
                                categorySelect.append(`
                                    <option value="${category.category_id}" 
                                        ${category.category_id == selectedCategoryId ? 'selected' : ''}>
                                        ${category.name}
                                    </option>
                                `);
                            });
                    }
                    categorySelect.prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    debugLog('Error loading categories for edit:', error);
                    showAlert('Error loading categories', 'error');
                    categorySelect.prop('disabled', false);
                }
            });
        }

        function deleteBudget(categoryId, categoryName) {
            if (confirm(`Are you sure you want to delete the budget for ${categoryName}?`)) {
                // Show loading state
                const deleteButton = $(`button[data-delete-budget="${categoryId}"]`);
                const originalHtml = deleteButton.html();
                deleteButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

                $.ajax({
                    url: 'api.php?action=delete_budget',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ category_id: parseInt(categoryId) }),
                    success: function(response) {
                        if (response.success) {
                            showAlert('Budget deleted successfully', 'success');
                            loadSummary(); // Refresh the budget display
                        } else {
                            showAlert(response.error || 'Error deleting budget', 'error');
                            // Restore button state
                            deleteButton.prop('disabled', false).html(originalHtml);
                        }
                    },
                    error: function(xhr, status, error) {
                        showAlert('Error deleting budget: ' + error, 'error');
                        console.error('Budget delete error:', error);
                        // Restore button state
                        deleteButton.prop('disabled', false).html(originalHtml);
                    }
                });
            }
        }

       
    </script>
</body>

</html> 