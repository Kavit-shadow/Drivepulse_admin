<?php

include('../../includes/authentication.php');
authenticationAdmin('../../');

date_default_timezone_set('Asia/Kolkata');
// $conn = mysqli_connect("localhost", "root", "", "billing");
include('../../config.php');


function convertDMY($dateString)
{
    if ($dateString === "0000-00-00") {
        echo "00-00-00";
    } else {

        $date = new DateTime($dateString);
        $formattedDate = $date->format("d-m-Y");
        echo $formattedDate;
    }
}


?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

   
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://kit.fontawesome.com/3db79b918b.js" crossorigin="anonymous"></script>

    <!-- Modern UI Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script src="https://cdn.jsdelivr.net/npm/luxon@2.0.2/build/global/luxon.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon@1.0.0"></script>

    
    <style>
        :root {
            --primary-color: #46abcc;
            --primary-hover: #1397c2;
            --background-color: #f4f7fa;
            --card-background: #ffffff;
            --text-primary: #2d3748;
            --text-secondary: #4a5568;
            --border-color: #e2e8f0;
            --spacing-xs: 0.5rem;
            --spacing-sm: 1rem;
            --spacing-md: 1.5rem;
            --spacing-lg: 2rem;
            --border-radius: 0.5rem;
            --box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        body {
            background-color: var(--background-color);
            min-height: 100vh;
        }

        .analytics-container {
            margin: var(--spacing-lg) auto;
            padding: var(--spacing-sm);
            display: grid;
            gap: var(--spacing-lg);
            width: 100%;
            max-width: 1920px;
        }

        @media (min-width: 640px) {
            .analytics-container {
                grid-template-columns: repeat(1, 1fr);
                padding: var(--spacing-md);
            }
        }

        @media (min-width: 1024px) {
            .analytics-container {
                grid-template-columns: repeat(2, 1fr);
                padding: var(--spacing-lg);
            }
        }

        @media (min-width: 1536px) {
            .analytics-container {
                grid-template-columns: repeat(2, 1fr);
                gap: var(--spacing-lg);
            }
        }

        .chart-card {
            background: var(--card-background);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: var(--spacing-md);
            transition: transform 0.2s ease-in-out;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .chart-card:hover {
            transform: translateY(-2px);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-md);
            flex-wrap: wrap;
            gap: var(--spacing-sm);
        }

        .chart-controls {
            display: flex;
            gap: var(--spacing-sm);
            flex-wrap: wrap;
        }

        .chart-controls button {
            background-color: var(--card-background);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: var(--spacing-xs) var(--spacing-sm);
            font-size: 0.875rem;
            color: var(--text-primary);
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .chart-controls button:hover {
            background-color: var(--grey);
        }

        .chart-controls button i {
            font-size: 1rem;
        }

        .chart-controls .ml-2 {
            margin-left: 0.5rem;
        }

        .select-container {
            position: relative;
            min-width: 150px;
            flex: 1;
            margin-right: 10px;
        }

        .select-container:last-child {
            margin-right: 0;
        }

        select {
            width: 100%;
            appearance: none;
            background-color: var(--card-background);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: var(--spacing-xs) var(--spacing-lg) var(--spacing-xs) var(--spacing-sm);
            font-size: 0.875rem;
            color: var(--text-primary);
            cursor: pointer;
            transition: border-color 0.2s ease-in-out;
        }

        select:hover {
            border-color: var(--blue);
        }

        select:focus {
            outline: none;
            border-color: var(--blue);
            box-shadow: 0 0 0 2px rgba(70, 171, 204, 0.2);
        }

        .select-container::after {
            content: '';
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 5px solid var(--text-primary);
            pointer-events: none;
        }

        @media (max-width: 640px) {
            .chart-controls {
                flex-direction: column;
            }

            .select-container {
                margin-right: 0;
                margin-bottom: 10px;
            }

            .select-container:last-child {
                margin-bottom: 0;
            }
        }

        .chart-container {
            flex: 1;
            min-height: 300px;
            position: relative;
            width: 100%;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin: 1.5rem 0;
            padding: var(--spacing-lg);
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            background: #f8f9fa;
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-icon i {
            font-size: 1.5rem;
            color: #4361ee;
        }

        .stat-info {
            flex: 1;
        }

        .stat-info h3 {
            color: #6c757d;
            font-size: 0.875rem;
            margin: 0 0 0.5rem 0;
            font-weight: 500;
        }

        .stat-value {
            color: #2b2d42;
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }

        @media (min-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (min-width: 1280px) {
            .stats-grid {
                grid-template-columns: repeat(7, 1fr);
            }
        }

        .enhanced-analytics {
            padding: var(--spacing-sm);
            max-width: 1920px;
            margin: 0 auto;
        }

        .metrics-grid {
            display: grid;
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-lg);
        }

        @media (min-width: 768px) {
            .metrics-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .metric-card {
            background: var(--card-background);
            border-radius: var(--border-radius);
            padding: var(--spacing-md);
            box-shadow: var(--box-shadow);
        }

        .metric-content {
            display: grid;
            gap: var(--spacing-sm);
        }

        @media (min-width: 480px) {
            .metric-content {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 768px) {
            .metric-content {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .metric-item {
            text-align: center;
            padding: var(--spacing-sm);
            background: var(--background-color);
            border-radius: var(--border-radius);
        }

        .insights-section {
            background: var(--card-background);
            border-radius: var(--border-radius);
            padding: var(--spacing-md);
            margin-top: var(--spacing-md);
            box-shadow: var(--box-shadow);
        }

        .insights-grid {
            display: grid;
            gap: var(--spacing-sm);
        }

        @media (min-width: 640px) {
            .insights-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .insights-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (min-width: 1280px) {
            .insights-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        .insight-card {
            background: var(--background-color);
            border-radius: var(--border-radius);
            padding: var(--spacing-sm);
            height: 100%;
        }

        .insight-card h4 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: var(--spacing-sm);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .insight-stat {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-xs);
            font-size: 0.875rem;
        }

        @media (max-width: 480px) {
            .insight-stat {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.25rem;
            }

            .chart-header {
                flex-direction: column;
            }

            .chart-controls {
            width: 100%;
            }

            select, button {
                width: 100%;
            }

            .stat-card {
                padding: var(--spacing-sm);
            }

            .stat-card h3 {
                font-size: 0.75rem;
            }

            .stat-card p {
                font-size: 1rem;
            }
        }

        /* Loading State Styles */
        .loading {
            position: relative;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: inherit;
        }

        /* Smooth Transitions */
        .chart-card,
        .stat-card,
        .metric-card,
        .insight-card {
            transition: all 0.3s ease-in-out;
        }

        /* Print Styles */
        @media print {
            .chart-card,
            .stat-card,
            .metric-card,
            .insight-card {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .chart-controls,
        button {
                display: none;
            }

            body {
                background: white;
            }

            .analytics-container,
            .enhanced-analytics {
                padding: 0;
            }
        }

        .advanced-analytics {
            padding: var(--spacing-md);
            margin-top: 2rem;
        }

        .analytics-section {
            background: var(--light);
            border-radius: 20px;
            padding: var(--spacing-lg);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--dark);
        }

        .insight-card {
            background: var(--grey);
            padding: 1.5rem;
            border-radius: 15px;
            transition: transform 0.2s ease-in-out;
        }

        .insight-card:hover {
            transform: translateY(-2px);
        }

        .insight-card h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--dark);
        }

        .insight-content {
            display: grid;
            gap: 1rem;
        }

        .metric {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem;
            background: var(--light);
            border-radius: 10px;
        }

        .metric span {
            color: var(--dark-grey);
            font-size: 0.9rem;
        }

        .metric p {
            font-weight: 600;
            color: var(--blue);
            font-size: 1.1rem;
        }

        .trainer-performance-table,
        .slot-efficiency-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .trainer-performance-table th,
        .trainer-performance-table td,
        .slot-efficiency-table th,
        .slot-efficiency-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--grey);
        }

        .trainer-performance-table th,
        .slot-efficiency-table th {
            font-weight: 600;
            background: var(--grey);
            color: var(--dark);
        }

        .trainer-performance-table tr:hover,
        .slot-efficiency-table tr:hover {
            background: var(--grey);
        }

        .chart-container {
            height: 300px;
            margin-top: 1rem;
        }

        @media (max-width: 768px) {
            .advanced-analytics {
                padding: var(--spacing-sm);
            }

            .analytics-section {
                padding: 1rem;
            }

            .section-title {
                font-size: 20px;
            }

            .insight-card h3 {
            font-size: 16px;
            }

            .metric {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .trainer-performance-table,
            .slot-efficiency-table {
                font-size: 0.9rem;
            }

            .trainer-performance-table th,
            .trainer-performance-table td,
            .slot-efficiency-table th,
            .slot-efficiency-table td {
                padding: 0.75rem;
            }

            .chart-container {
                height: 250px;
            }
        }

        @media print {
            .advanced-analytics {
                padding: 0;
            }

            .insight-card {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .chart-container {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }

        /* Mobile Overlay Styles */
        .mobile-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .mobile-overlay.show {
            opacity: 1;
        }

        .overlay-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: var(--light);
            padding: 2rem;
            border-radius: 1rem;
            text-align: center;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .overlay-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem auto;
            opacity: 0.8;
            display: block;
        }

        .overlay-content h2 {
            color: var(--dark);
            font-size: 1.5rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .overlay-content p {
            color: var(--text-secondary);
            margin-bottom: 1rem;
            font-size: 1rem;
            line-height: 1.5;
        }

        .overlay-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .continue-btn, .back-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
            width: 100%;
            text-align: center;
            font-size: 1rem;
        }

        .continue-btn {
            background-color: var(--blue);
            color: white;
            border: none;
            cursor: pointer;
        }

        .continue-btn:hover {
            background-color: var(--primary-hover);
        }

        .back-btn {
            background-color: var(--grey);
            color: var(--dark);
            text-decoration: none;
        }

        .back-btn:hover {
            background-color: var(--dark-grey);
            color: var(--light);
        }

        @media (max-width: 480px) {
            .overlay-content {
                padding: 1.5rem;
            }

            .overlay-content h2 {
                font-size: 1.25rem;
            }

            .overlay-content p {
                font-size: 0.875rem;
            }

            .continue-btn, .back-btn {
                padding: 0.625rem 1.25rem;
                font-size: 0.875rem;
            }
        }

        /* Dark mode support */
        body.dark .overlay-content {
            background-color: var(--dark);
        }

        body.dark .overlay-content h2 {
            color: var(--light);
        }

        body.dark .overlay-content p {
            color: var(--grey);
        }

        body.dark .back-btn {
            background-color: var(--dark-grey);
            color: var(--light);
        }

        body.dark .back-btn:hover {
            background-color: var(--grey);
            color: var(--dark);
        }

        /* Fullscreen Modal Styles */
        .fullscreen-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: var(--light);
            z-index: 9999;
            opacity: 0;
            transition: all 0.3s ease;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 1rem;
            box-sizing: border-box;
        }

        .fullscreen-modal.show {
            opacity: 1;
            display: flex;
        }

        .fullscreen-chart-container {
            width: 95%;
            height: 75%;
            position: relative;
            background-color: var(--light);
            border-radius: var(--border-radius);
            padding: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 1600px;
            margin: 0 auto;
        }

        .fullscreen-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
            width: 95%;
            max-width: 1600px;
            background-color: var(--light);
            padding: 0.75rem;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .fullscreen-controls button,
        .fullscreen-controls .chart-select {
            flex: 0 1 auto;
            min-width: 120px;
            max-width: 200px;
            height: 36px;
            padding: 0.5rem;
            font-size: 0.875rem;
        }

        /* Tablet Styles */
        @media (min-width: 768px) and (max-width: 1024px) {
            .fullscreen-chart-container {
                width: 90%;
                height: 70%;
                padding: 1.25rem;
            }

            .fullscreen-controls {
                width: 90%;
                padding: 1rem;
            }

            .fullscreen-controls button,
            .fullscreen-controls .chart-select {
                min-width: 140px;
            }
        }

        /* Desktop Styles */
        @media (min-width: 1025px) {
            .fullscreen-chart-container {
                width: 85%;
                height: 80%;
                padding: 1.5rem;
            }

            .fullscreen-controls {
                width: 85%;
                padding: 1.25rem;
                gap: 1rem;
            }

            .fullscreen-controls button,
            .fullscreen-controls .chart-select {
                min-width: 160px;
                height: 40px;
                font-size: 1rem;
            }
        }

        /* Large Desktop Styles */
        @media (min-width: 1440px) {
            .fullscreen-chart-container {
                width: 80%;
                max-height: 85%;
            }

            .fullscreen-controls {
                width: 80%;
            }
        }

        /* Handle shorter screens */
        @media (max-height: 800px) {
            .fullscreen-chart-container {
                height: 65%;
            }

            .fullscreen-controls {
                padding: 0.5rem;
            }
        }

        /* Ensure controls are usable on very small screens */
        @media (max-width: 480px) {
            .fullscreen-chart-container {
                width: 98%;
                height: 60%;
                padding: 0.75rem;
            }

            .fullscreen-controls {
                width: 98%;
                padding: 0.5rem;
                gap: 0.25rem;
            }

            .fullscreen-controls button,
            .fullscreen-controls .chart-select {
                min-width: 110px;
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }

            .fullscreen-close {
                top: 0.5rem;
                right: 0.5rem;
                width: 32px;
                height: 32px;
                font-size: 1.25rem;
            }
        }

        .fullscreen-controls button {
            background-color: var(--card-background);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            color: var(--text-primary);
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .fullscreen-controls button:hover {
            background-color: var(--grey);
        }

        .fullscreen-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--grey);
            border: none;
            color: var(--dark);
            font-size: 1.5rem;
            cursor: pointer;
            z-index: 10000;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .fullscreen-close:hover {
            background-color: var(--dark-grey);
            color: var(--light);
        }

        .fullscreen-controls .chart-select {
            background-color: var(--card-background);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            color: var(--text-primary);
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
            outline: none;
        }

        .fullscreen-controls .chart-select:hover {
            background-color: var(--grey);
        }

        .fullscreen-controls .chart-select option {
            background-color: var(--light);
            color: var(--dark);
            padding: 0.5rem;
        }

        /* Dark mode support */
        body.dark .fullscreen-modal {
            background-color: var(--dark);
        }

        body.dark .fullscreen-chart-container {
            background-color: var(--grey);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        body.dark .fullscreen-controls {
            background-color: var(--grey);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        body.dark .fullscreen-close {
            background-color: var(--dark-grey);
            color: var(--light);
        }

        body.dark .fullscreen-close:hover {
            background-color: var(--blue);
        }

        body.dark .fullscreen-controls button {
            background-color: var(--dark);
            color: var(--light);
            border-color: var(--dark-grey);
        }

        body.dark .fullscreen-controls button:hover {
            background-color: var(--dark-grey);
        }

        body.dark .fullscreen-controls .chart-select {
            background-color: var(--dark);
            color: var(--light);
            border-color: var(--dark-grey);
        }

        body.dark .fullscreen-controls .chart-select:hover {
            background-color: var(--dark-grey);
        }

        body.dark .fullscreen-controls .chart-select option {
            background-color: var(--dark);
            color: var(--light);
        }

        /* Tablet Styles */
        @media (min-width: 768px) and (max-width: 1024px) {
            .fullscreen-chart-container {
                width: 90%;
                height: 70%;
                padding: 1.25rem;
            }

            .fullscreen-controls {
                width: 90%;
                padding: 1rem;
            }

            .fullscreen-controls button,
            .fullscreen-controls .chart-select {
                min-width: 140px;
            }
        }

        /* Desktop Styles */
        @media (min-width: 1025px) {
            .fullscreen-chart-container {
                width: 85%;
                height: 80%;
                padding: 1.5rem;
            }

            .fullscreen-controls {
                width: 85%;
                padding: 1.25rem;
                gap: 1rem;
            }

            .fullscreen-controls button,
            .fullscreen-controls .chart-select {
                min-width: 160px;
                height: 40px;
                font-size: 1rem;
            }
        }

        /* Large Desktop Styles */
        @media (min-width: 1440px) {
            .fullscreen-chart-container {
                width: 80%;
                max-height: 85%;
            }

            .fullscreen-controls {
                width: 80%;
            }
        }

        /* Handle shorter screens */
        @media (max-height: 800px) {
            .fullscreen-chart-container {
                height: 65%;
            }

            .fullscreen-controls {
                padding: 0.5rem;
            }
        }

        /* Ensure controls are usable on very small screens */
        @media (max-width: 480px) {
            .fullscreen-chart-container {
                width: 98%;
                height: 60%;
                padding: 0.75rem;
            }

            .fullscreen-controls {
                width: 98%;
                padding: 0.5rem;
                gap: 0.25rem;
            }

            .fullscreen-controls button,
            .fullscreen-controls .chart-select {
                min-width: 110px;
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }

            .fullscreen-close {
                top: 0.5rem;
                right: 0.5rem;
                width: 32px;
                height: 32px;
                font-size: 1.25rem;
            }
        }

        .insights-section {
            background: var(--card-background);
            border-radius: var(--border-radius);
            padding: var(--spacing-lg);
            margin-top: var(--spacing-lg);
            box-shadow: var(--box-shadow);
            transition: all 0.3s ease;
        }

        .insights-section:hover {
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        .insights-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-md);
            padding-bottom: var(--spacing-sm);
            border-bottom: 2px solid var(--border-color);
        }

        .insights-title {
            color: var(--text-primary);
            font-size: 1.25rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .insights-title i {
            color: var(--primary-color);
            font-size: 1.1em;
        }

        .insights-actions {
            display: flex;
            gap: var(--spacing-sm);
        }

        .refresh-btn {
            background: transparent;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: var(--border-radius);
            transition: all 0.2s ease;
        }

        .refresh-btn:hover {
            color: var(--primary-color);
            background: rgba(70, 171, 204, 0.1);
        }

        .refresh-btn i {
            font-size: 1rem;
        }

        .insights-grid {
            display: grid;
            gap: var(--spacing-md);
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        }

        .insight-card {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: var(--spacing-md);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .insight-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--primary-color);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .insight-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .insight-card:hover::before {
            opacity: 1;
        }

        .insight-card h4 {
            color: var(--text-primary);
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: var(--spacing-md);
            padding-bottom: var(--spacing-sm);
            border-bottom: 1px solid var(--border-color);
        }

        .insight-stat {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            transition: all 0.2s ease;
        }

        .insight-stat:hover {
            background: rgba(70, 171, 204, 0.05);
            padding: 0.5rem;
            margin: 0 -0.5rem;
            border-radius: var(--border-radius);
        }

        .insight-stat span:first-child {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .insight-stat span:last-child {
            color: var(--text-primary);
            font-weight: 500;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .insights-section {
                padding: var(--spacing-md);
            }

            .insights-grid {
                grid-template-columns: 1fr;
            }

            .insight-card {
                margin-bottom: var(--spacing-sm);
            }
        }

        @keyframes refresh-spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .refresh-btn.spinning i {
            animation: refresh-spin 1s linear infinite;
        }
    </style>

    <!-- Add SweetAlert library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="shortcut icon" type="image/png" href="../../assets/logo.png" />
    <title>Analytics</title>
    <!-- My CSS -->
   <style> 
@import url('https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Poppins:wght@400;500;600;700&display=swap');

* {
	margin: 0;
	padding: 0;
	box-sizing: border-box;
}

a {
	text-decoration: none;
}

li {
	list-style: none;
}

:root {
	--poppins: 'Poppins', sans-serif;
	--lato: 'Lato', sans-serif;

	--light: #F9F9F9;
	--blue: #46abcc;
	--light-blue: #CFE8FF;
	--grey: #eee;
	--dark-grey: #AAAAAA;
	--dark: #342E37;
	--yellow: #FFCE26;
	--light-yellow: #FFF2C6;
	--orange: #FD7238;
	--light-orange: #FFE0D3;
	--red: red;
	--green: green;
}

html {
	overflow-x: hidden;
}

body.dark {
	--light: #0C0C1E;
	--grey: #060714;
	--dark: #FBFBFB;
}




body {
	background: var(--grey);
	overflow-x: hidden;
}





/* SIDEBAR */
#sidebar {
	position: fixed;
	top: 0;
	left: 0;
	width: 280px;
	height: 100%;
	background: var(--light);
	z-index: 2000;
	font-family: var(--lato);
	transition: .3s ease;
	overflow-x: hidden;
	scrollbar-width: none;
}

#sidebar::--webkit-scrollbar {
	display: none;
}

#sidebar.hide {
	width: 60px;
}

#sidebar .brand {

	font-size: 24px;
	font-weight: 700;
	height: 56px;
	display: flex;
	align-items: center;
	color: var(--blue);
	position: sticky;
	top: 0;
	left: 0;
	background: var(--light);
	z-index: 500;
	padding-bottom: 20px;
	box-sizing: content-box;
}

#sidebar .brand .bx {
	min-width: 60px;
	display: flex;
	justify-content: center;
}

#sidebar .side-menu {
	width: 100%;
	margin-top: 48px;
}

#sidebar .side-menu li {
	height: 48px;
	background: transparent;
	margin-left: 6px;
	border-radius: 48px 0 0 48px;
	padding: 4px;
}


#sidebar .side-menu li.active {
	background: var(--grey);
	position: relative;
}

#sidebar .side-menu li.active::before {
	content: '';
	position: absolute;
	width: 40px;
	height: 40px;
	border-radius: 50%;
	top: -40px;
	right: 0;
	box-shadow: 20px 20px 0 var(--grey);
	z-index: -1;
}

#sidebar .side-menu li.active::after {
	content: '';
	position: absolute;
	width: 40px;
	height: 40px;
	border-radius: 50%;
	bottom: -40px;
	right: 0;
	box-shadow: 20px -20px 0 var(--grey);
	z-index: -1;
}

#sidebar .side-menu li a {
	width: 100%;
	height: 100%;
	background: var(--light);
	display: flex;
	gap: 15px;
	align-items: center;
	border-radius: 48px;
            font-size: 16px;
	color: var(--dark);
	white-space: nowrap;
	overflow-x: hidden;
}

#sidebar .side-menu.top li.active a {
	color: var(--blue);
}

#sidebar.hide .side-menu li a {
	width: calc(48px - (4px * 2));
	transition: width .3s ease;
}

#sidebar .side-menu li a.logout {
	color: var(--red);
}

#sidebar .side-menu.top li a:hover {
	color: var(--blue);
}

#sidebar .side-menu li a .bx {
	min-width: calc(60px - ((4px + 6px) * 2));
	display: flex;
	justify-content: center;
}

/* SIDEBAR */





/* CONTENT */
#content {
	position: relative;
	width: calc(100% - 280px);
	left: 280px;
	transition: .3s ease;
}

#sidebar.hide~#content {
	width: calc(100% - 60px);
	left: 60px;
}




/* NAVBAR */
#content nav {
	display: flex;
	justify-content: space-between;
	height: 60px;
	background: var(--light);
	padding: 0 24px;
	display: flex;
	align-items: center;
	grid-gap: 24px;
	font-family: var(--lato);
	position: sticky;
	top: 0;
	left: 0;
	z-index: 1000;
	text-align: center;
}

#content nav h5 {
	background: var(--blue);
            color: #fff;
	border-radius: 10px;
	padding: 2px 15px;
}

#content nav::before {
	content: '';
	position: absolute;
	width: 40px;
	height: 40px;
	bottom: -40px;
	left: 0;
	border-radius: 50%;
	box-shadow: -20px -20px 0 var(--light);
}

#content nav a {
	color: var(--dark);
}

#content nav .bx.bx-menu {
            cursor: pointer;
	color: var(--dark);
}

#content nav .nav-link {
            font-size: 16px;
	transition: .3s ease;
}

#content nav .nav-link:hover {
	color: var(--blue);
}

#content nav form {
	max-width: 400px;
	width: 100%;
	margin-right: auto;
}

#content nav form .form-input {
	display: flex;
	align-items: center;
	height: 36px;
}

#content nav form .form-input input {
	flex-grow: 1;
	padding: 0 16px;
	height: 100%;
	border: none;
	background: var(--grey);
	border-radius: 36px 0 0 36px;
	outline: none;
	width: 100%;
	color: var(--dark);
}

#content nav form .form-input button {
	width: 36px;
	height: 100%;
	display: flex;
	justify-content: center;
	align-items: center;
	background: var(--blue);
	color: var(--light);
	font-size: 18px;
	border: none;
	outline: none;
	border-radius: 0 36px 36px 0;
            cursor: pointer;
        }

#content nav .notification {
	font-size: 20px;
	position: relative;
}

#content nav .notification .num {
	position: absolute;
	top: -6px;
	right: -6px;
	width: 20px;
	height: 20px;
	border-radius: 50%;
	border: 2px solid var(--light);
	background: var(--red);
	color: var(--light);
	font-weight: 700;
	font-size: 12px;
	display: flex;
	justify-content: center;
	align-items: center;
}

#content nav .profile img {
	width: 36px;
	height: 36px;
	object-fit: cover;
	border-radius: 50%;
}

#content nav .switch-mode {
	display: block;
	min-width: 50px;
	height: 25px;
	border-radius: 25px;
	background: var(--grey);
	cursor: pointer;
	position: relative;
}

#content nav .switch-mode::before {
	content: '';
	position: absolute;
	top: 2px;
	left: 2px;
	bottom: 2px;
	width: calc(25px - 4px);
	background: var(--blue);
	border-radius: 50%;
	transition: all .3s ease;
}

#content nav #switch-mode:checked+.switch-mode::before {
	left: calc(100% - (25px - 4px) - 2px);
}

/* NAVBAR */





/* MAIN */
#content main {
	width: 100%;
	padding: 36px 24px;
	font-family: var(--poppins);
	height: 100vh;
	max-height: calc(100vh - 56px);
	overflow-y: auto;
}

#content main .head-title {
	display: flex;
	align-items: center;
	justify-content: space-between;
	grid-gap: 16px;
	flex-wrap: wrap;
}

#content main .head-title .left h1 {
	font-size: 36px;
	font-weight: 600;
	margin-bottom: 10px;
	color: var(--dark);
}

#content main .head-title .left .breadcrumb {
	display: flex;
	align-items: center;
	grid-gap: 16px;
}

#content main .head-title .left .breadcrumb li {
	color: var(--dark);
}

#content main .head-title .left .breadcrumb li a {
	color: var(--dark-grey);

}

#content main .head-title .left .breadcrumb li a.active {
	color: var(--blue);
	pointer-events: unset;
}

#content main .head-title .btn-download {
	height: 36px;
	padding: 0 16px;
	border-radius: 36px;
	background: green;
	color: var(--light);
	display: flex;
	justify-content: center;
	align-items: center;
	grid-gap: 10px;
	font-weight: 500;
}




#content main .box-info {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
	grid-gap: 24px;
	margin-top: 20px;
}

#content main .box-info li {
	padding: 24px;
	background: var(--light);
	border-radius: 20px;
	display: flex;
	align-items: center;
	grid-gap: 24px;
}

#content main .box-info li .bx {
	width: 80px;
	height: 80px;
	border-radius: 10px;
	font-size: 36px;
	display: flex;
	justify-content: center;
	align-items: center;
}

#content main .box-info li:nth-child(1) .bx {
	background: #FFE0D3;
	color: #FD7238;
}

#content main .box-info li:nth-child(2) .bx {
	background: #CFE8FF;
	color: #46abcc;
}

#content main .box-info li:nth-child(3) .bx {
	background: #FFF2C6;
	color: #FFCE26;
}

#content main .box-info li:nth-child(4) .bx {
	background: #90EE90;
	color: #008000;
}

#content main .box-info li .text h3 {
	font-size: 24px;
	font-weight: 600;
	color: var(--dark);
}

#content main .box-info li .text p {
	color: var(--dark);
}





#content main .table-data {
	display: flex;
	flex-wrap: wrap;
	grid-gap: 24px;
	margin-top: 24px;
	width: 100%;
	color: var(--dark);
	flex-direction: column;
}

#content main .table-data>div {
	border-radius: 20px;
	background: var(--light);
	padding: 24px;
	overflow-x: auto;
}

#content main .table-data .head {
	display: flex;
	align-items: center;
	grid-gap: 16px;
	margin-bottom: 24px;
	border-radius: 13px;
}

#content main .table-data .head h3 {
	margin-right: auto;
	font-size: 24px;
	font-weight: 600;
}

#content main .table-data .head .bx {
	cursor: pointer;
}

#content main .table-data .order {
	flex-grow: 1;
	flex-basis: 500px;
}

#content main .table-data .order table {
	width: 100%;
	border-collapse: collapse;
}

#content main .table-data .order table th {
	padding-bottom: 12px;
	font-size: 13px;
	text-align: left;
	border-bottom: 1px solid var(--grey);
}

#content main .table-data .order table td {
	padding: 16px 0;
}


#content main .table-data .order table td img {
	width: 36px;
	height: 36px;
	border-radius: 50%;
	object-fit: cover;
}

#content main .table-data .order table tbody tr:hover {
	background: var(--grey);
}

#content main .table-data .order table tr td .status {
	font-size: 10px;
	padding: 6px 16px;
	color: var(--light);
	border-radius: 20px;
	font-weight: 700;
}



#content main .table-data .order table tr td .status.completed {
	background: var(--red);
}

#content main .table-data .order table tr td .btn {
	font-size: 10px;
	padding: 6px 16px;
	color: var(--light);
	border-radius: 20px;
	font-weight: 700;
	background: green;
}

#content main .table-data .order table tr td .btn-delete {
	font-size: 10px;
	padding: 6px 16px;
	color: var(--light);
	border-radius: 20px;
	font-weight: 700;
	background: red;
}

#content main .table-data .order table tr td .status.process {
	background: var(--green);
}

#content main .table-data .order table tr td .status.pending {
	background: var(--orange);
}


#content main .table-data .todo {
	flex-grow: 1;
	flex-basis: 300px;
}

#content main .table-data .todo .todo-list {
	width: 100%;
}

#content main .table-data .todo .todo-list li {
	width: 100%;
	margin-bottom: 16px;
	background: var(--grey);
	border-radius: 10px;
	padding: 14px 20px;
	display: flex;
	justify-content: space-between;
	align-items: center;
}

#content main .table-data .todo .todo-list li .bx {
	cursor: pointer;
}

#content main .table-data .todo .todo-list li.completed {
	border-left: 10px solid var(--blue);
}

#content main .table-data .todo .todo-list li.not-completed {
	border-left: 10px solid var(--orange);
}

#content main .table-data .todo .todo-list li:last-child {
	margin-bottom: 0;
}

/* #content main .table-data .order table tr td:first-child {
	display: flex;
	align-items: center;
	grid-gap: 12px;
	padding-left: 6px;
} */


/* MAIN */
/* CONTENT */





@media (max-width: 940px) {
	#content main .table-data .order table td {
		font-size: 12px;
	}
}





@media only screen and (max-width: 768px) {
            #sidebar {
                width: 200px;
            }

            .side-menu .text {

                font-size: 12px;
                font-weight: 700;

            }

            #content {
                width: calc(100% - 60px);
                left: 200px;
            }

            #content nav .nav-link {
                display: none;
            }

            #sidebar .brand .text h4 {

                font-size: 17px;
            }
	#sidebar .brand .text h6 {
		font-size: 15px;
            }

            #content main .head-title .left .breadcrumb {
                font-size: 13px;
            }

	.view {

		font-size: 11px;

	}

	#content main .table-data .order table td {
		font-size: 10px;
	}

	#content main .table-data .order table th {
		font-size: 10px;
	}
}







@media screen and (max-width: 576px) {

	.text {
		font-size: 12px;
	}

	#content nav form .form-input input {
		display: none;
	}

	#content nav form .form-input button {
		width: auto;
		height: auto;
		background: transparent;
		border-radius: none;
		color: var(--dark);
	}

	#content nav form.show .form-input input {
		display: block;
		width: 100%;
	}

	#content nav form.show .form-input button {
		width: 36px;
		height: 100%;
		border-radius: 0 36px 36px 0;
		color: var(--light);
		background: var(--red);
	}

	#content nav form.show~.notification,
	#content nav form.show~.profile {
		display: none;
	}

	#content main .box-info {
		grid-template-columns: 1fr;
	}

	#content main .table-data .head {
		min-width: 420px;
	}

	#content main .table-data .order table {
		min-width: 420px;
	}

	#content main .table-data .todo .todo-list {
		min-width: 420px;
	}

	.view {
		padding: 2px;
		font-size: 9px;

	}

}





   </style>
    <link rel="stylesheet" href="../../css/sideBarFooter.css">
</head>

<body>
    <!-- Mobile Overlay -->
    <div id="mobile-overlay" class="mobile-overlay">
        <div class="overlay-content">
            <img src="../../assets/logo.png" alt="Desktop Icon" class="overlay-icon">
            <h2>Best Viewed on Larger Screens</h2>
            <p>This analytics dashboard is optimized for tablet and desktop viewing.</p>
            <p>For the best experience, please switch to a device with a larger screen.</p>
            <div class="overlay-buttons">
                <button id="continue-anyway" class="continue-btn">Continue Anyway</button>
                <a href="../" class="back-btn">Go Back to Dashboard</a>
            </div>
        </div>
    </div>

    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand">
            <i class='bx bxs-car'></i>

            <span class="text">
                <h6><span>Admin</span></h6>
                <h4>Welcome <span>
                        <?php echo $_SESSION['admin_name'] ?>
                    </span></h4>
            </span>
        </a>
        <ul class="side-menu top">
            <li>
                <a href="../">
                    <i class='bx bxs-dashboard'></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="../admissionForm.php">
                    <i class='bx bxs-book-bookmark'></i>
                    <span class="text">Book Admission</span>
                </a>
            </li>
            <li class="active">
                <a href="../analytics">
                    <i class='bx bxs-bar-chart-alt-2'></i>
                    <span class="text">Analytics</span>
                </a>
            </li>
             
            <li class="search">
                <a href="../search.php">
                    <i class='bx bx-search-alt-2'></i>
                    <span class="text">Search</span>
                </a>
            </li>
            <li>
                <a href="../sortData/">
                    <!-- <i class='bx bxs-bar-chart-alt-2'></i> -->
                    <i class='bx bxs-data'></i>
                    <span class="text">Customers Data</span>
                </a>
            </li>
            <li class="time-table">
                <a href="../timetable/">
                    <i class='bx bx-table'></i>
                    <span class="text">Time Table</span>
                </a>
            </li>
            <li>
                <a href="../dueCustomers/">
                    <i class='bx bxs-user'></i>
                    <span class="text">Pending Payment</span>
                </a>
            </li>
            <li>
                <a href="../mailSender/">
                    <i class='bx bx-mail-send'></i>
                    <span class="text">Mail System</span>
                </a>
            </li>
            <li>
                <a href="../manageUsers/">
                    <i class='bx bxs-group'></i>
                    <span class="text">Manage Users</span>
                </a>
            </li>
            <li>
                <a href="../createVehicle/">
                    <i class='bx bxs-car'></i>
                    <span class="text">Add New Vehicle</span>
                </a>
            </li>
            <li>
                <a href="../employeeManagement/">
                    <i class='bx bxs-id-card'></i>
                    <span class="text">Employee Management</span>
                </a>
            </li>
            <li>
                <a href="../liveTrainings/">
                    <i class='bx bxs-videos'></i>
                    <span class="text">Live Trainings</span>
                </a>
            </li>
        </ul>
        <ul class="side-menu">
            <!-- <li>
                <a href="#">
                    <i class='bx bxs-cog'></i>
                    <span class="text">Settings</span>
                </a>
            </li> -->
            <li>
                <a href="../../logout.php" class="logout">
                    <i class='bx bxs-log-out-circle'></i>
                    <span class="text">Logout</span>
                </a>
            </li>
        </ul>
        
  
        </div>
    </section>
    <!-- SIDEBAR -->



    <!-- CONTENT -->
    <section id="content">
        <!-- NAVBAR -->
        <nav>
            <i class='bx bx-menu'></i>
            <!-- <a href="#" class="nav-link">Categories</a> -->
            <!-- <form action="" method="get">
                <div class="form-input">
                    <input type="search" name="search-query" placeholder="Search...">
                    <button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
                </div>
            </form> -->
            <!-- <input type="checkbox" id="switch-mode" hidden>
            <label for="switch-mode" class="switch-mode"></label>
            <a href="#" class="notification">
                <i class='bx bxs-bell'></i>
                <span class="num">8</span>
            </a> -->
            <span class="text">
                <h3><?php
                    include("../../configWeb.php");
                    echo $WebAppTitle;
                    ?></h3>
                <h5>Dashboard</h5>
            </span>
            <a href="../" class="profile">
                <img src="../../assets/logoBlack.png">
            </a>
        </nav>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Analytics</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a class="active" style=" color: #aaaaaa;" href="../">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="./">Analytics</a>
                        </li>
                    </ul>
                </div>
                <!-- <a href="Excel/?export=true" class="btn-download">
                    <i class='bx bxs-cloud-download'></i>
                    <span class="text">Data to Excel</span>
                </a> -->
            </div>

            <!-- Date Filter Section -->
            <div class="date-filter-section">
                <div class="filter-header">
                    <h2 class="filter-title">
                        <i class="fas fa-filter"></i>
                        Data Filters
                    </h2>
                    <button class="clear-filters" onclick="clearFilters()">
                        <i class="fas fa-times"></i>
                        Clear Filters
                    </button>
                </div>
                <div class="filter-content">
                    <div class="filter-group">
                        <label for="select-year">
                            <i class="fas fa-calendar-alt"></i>
                            Year
                        </label>
                        <div class="select-wrapper">
                            <select id="select-year" name="select-year">
                                <option value="all">All Years</option>
                                <?php
                                $years_query = "SELECT DISTINCT YEAR(date) AS year FROM cust_details ORDER BY year DESC";
                                $years_result = mysqli_query($conn, $years_query);
                                $currentYear = isset($_GET['select-year']) ? $_GET['select-year'] : date('Y');

                                while ($row = mysqli_fetch_assoc($years_result)) {
                                    $year = $row['year'];
                                    $selected = ($currentYear == $year) ? 'selected' : '';
                                    echo "<option value='$year' $selected>$year</option>";
                                }
                                ?>
                            </select>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="filter-group">
                        <label for="select-month">
                            <i class="fas fa-calendar-day"></i>
                            Month
                        </label>
                        <div class="select-wrapper">
                            <select id="select-month" name="select-month">
                                <option value="all">All Months</option>
                                <option value="01">January</option>
                                <option value="02">February</option>
                                <option value="03">March</option>
                                <option value="04">April</option>
                                <option value="05">May</option>
                                <option value="06">June</option>
                                <option value="07">July</option>
                                <option value="08">August</option>
                                <option value="09">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="filter-actions">
                        <button class="apply-filters" onclick="applyFilters()">
                            <i class="fas fa-check"></i>
                            Apply Filters
                        </button>
                    </div>
                </div>
            </div>

            <style>
                .date-filter-section {
                    background: var(--card-background);
                    border-radius: var(--border-radius);
                    padding: 1.5rem;
                    margin: 2rem;
                    box-shadow: var(--box-shadow);
                    transition: all 0.3s ease;
                }

                .date-filter-section:hover {
                    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
                }

                .filter-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 1.5rem;
                    padding-bottom: 0.75rem;
                    border-bottom: 2px solid var(--border-color);
                }

                .filter-title {
                    color: var(--text-primary);
                    font-size: 1.25rem;
                    font-weight: 600;
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                }

                .filter-title i {
                    color: var(--primary-color);
                }

                .clear-filters {
                    background: transparent;
                    border: 1px solid var(--border-color);
                    color: var(--text-secondary);
                    padding: 0.5rem 1rem;
                    border-radius: var(--border-radius);
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    transition: all 0.2s ease;
                }

                .clear-filters:hover {
                    color: var(--primary-color);
                    border-color: var(--primary-color);
                    background: rgba(70, 171, 204, 0.1);
                }

                .filter-content {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: 1.5rem;
                    align-items: end;
                }

                .filter-group {
                    display: flex;
                    flex-direction: column;
                    gap: 0.5rem;
                }

                .filter-group label {
                    color: var(--text-secondary);
                    font-size: 0.875rem;
                    font-weight: 500;
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                }

                .filter-group label i {
                    color: var(--primary-color);
                }

                .select-wrapper {
                    position: relative;
                    width: 100%;
                }

                .select-wrapper select {
                    width: 100%;
                    padding: 0.75rem;
                    padding-right: 2.5rem;
                    border: 1px solid var(--border-color);
                    border-radius: var(--border-radius);
                    background: white;
                    color: var(--text-primary);
                    font-size: 0.875rem;
                    appearance: none;
                    cursor: pointer;
                    transition: all 0.2s ease;
                }

                .select-wrapper select:hover,
                .select-wrapper select:focus {
                    border-color: var(--primary-color);
                    box-shadow: 0 0 0 2px rgba(70, 171, 204, 0.1);
                }

                .select-wrapper i {
                    position: absolute;
                    right: 1rem;
                    top: 50%;
                    transform: translateY(-50%);
                    color: var(--text-secondary);
                    pointer-events: none;
                    transition: transform 0.2s ease;
                }

                .select-wrapper select:focus + i {
                    transform: translateY(-50%) rotate(180deg);
                }

                .filter-actions {
                    display: flex;
                    justify-content: flex-end;
                    align-items: center;
                }

                .apply-filters {
                    background: var(--primary-color);
                    color: white;
                    border: none;
                    padding: 0.75rem 1.5rem;
                    border-radius: var(--border-radius);
                    font-weight: 500;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    transition: all 0.2s ease;
                }

                .apply-filters:hover {
                    background: var(--primary-hover);
                    transform: translateY(-1px);
                }

                @media (max-width: 768px) {
                    .filter-content {
                        grid-template-columns: 1fr;
                    }

                    .filter-actions {
                        justify-content: stretch;
                    }

                    .apply-filters {
                        width: 100%;
                        justify-content: center;
                    }
                }
            </style>

            <script>
                function clearFilters() {
                    document.getElementById('select-year').value = 'all';
                    document.getElementById('select-month').value = 'all';
                    applyFilters();
                }

                function applyFilters() {
                    const year = document.getElementById('select-year').value;
                    const month = document.getElementById('select-month').value;
                    
                    // Show loading state
                    const btn = document.querySelector('.apply-filters');
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Applying...';
                    btn.disabled = true;

                    // Update charts with new filters
                    updateCharts().then(() => {
                        // Reset button state
                        btn.innerHTML = originalText;
                        btn.disabled = false;

                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Filters Applied',
                            text: 'The data has been updated successfully',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }).catch(error => {
                        // Reset button state
                        btn.innerHTML = originalText;
                        btn.disabled = false;

                        // Show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to apply filters. Please try again.',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    });
                }

                // Add change event listeners to automatically apply filters
                document.getElementById('select-year').addEventListener('change', applyFilters);
                document.getElementById('select-month').addEventListener('change', applyFilters);
            </script>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Revenue</h3>
                        <p id="totalAmount" class="stat-value">₹0</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Received</h3>
                        <p id="totalPaid" class="stat-value">₹0</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Due</h3>
                        <p id="totalDue" class="stat-value">₹0</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Collection Rate</h3>
                        <p id="collectionRate" class="stat-value">0%</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Customers</h3>
                        <p id="totalCustomers" class="stat-value">0</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Average Revenue</h3>
                        <p id="avgSale" class="stat-value">₹0</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-arrow-trend-up"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Monthly Growth</h3>
                        <p id="monthlyGrowth" class="stat-value">0%</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Yearly Growth</h3>
                        <p id="yearlyGrowth" class="stat-value">0%</p>
                    </div>
                </div>
            </div>

            <style>
                .stats-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
                    gap: 1.5rem;
                    margin: 1.5rem 0;
                }

                .stat-card {
                    background: white;
                    border-radius: 12px;
                    padding: 1.5rem;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
                    display: flex;
                    align-items: center;
                    gap: 1rem;
                    transition: transform 0.2s ease, box-shadow 0.2s ease;
                }

                .stat-card:hover {
                    transform: translateY(-3px);
                    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
                }

                .stat-icon {
                    background: #f8f9fa;
                    width: 48px;
                    height: 48px;
                    border-radius: 12px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .stat-icon i {
                    font-size: 1.5rem;
                    color: #4361ee;
                }

                .stat-info {
                    flex: 1;
                }

                .stat-info h3 {
                    color: #6c757d;
                    font-size: 0.875rem;
                    margin: 0 0 0.5rem 0;
                    font-weight: 500;
                }

                .stat-value {
                    color: #2b2d42;
                    font-size: 1.5rem;
                    font-weight: 600;
                    margin: 0;
                }
            </style>

            <div class="analytics-container">


                <div class="chart-card">
                    <div class="chart-header">
                        <h2 class="text-xl font-semibold">Revenue Overview</h2>
                        <div class="chart-controls">
                          
                            <button id="change-chart-type">
                                <i class="bx bx-bar-chart"></i>
                                Change Chart Type
                            </button>
                            <button id="reset-zoom-revenue" class="ml-2" title="Reset chart to original zoom level">
                                <i class="bx bx-reset"></i>
                                Reset Zoom
                            </button>
                            <button id="fullscreen-revenue" class="ml-2" title="View chart in full screen">
                                <i class="bx bx-fullscreen"></i>
                                Full Screen
                            </button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                    <div class="text-xs text-gray-500 mt-2 text-center">
                        <i class="bx bx-info-circle"></i> Tip: Use mouse wheel to zoom, hold Shift + drag to pan, or double-click to reset zoom.
                    </div>
                </div>

                <div class="chart-card">
                    <div class="chart-header">
                        <h2 class="text-xl font-semibold">Vehicle Distribution</h2>
                        <div class="chart-controls">
                            <button id="change-distribution-view" class="text-sm">
                                <i class="bx bx-pie-chart"></i>
                                Toggle View
                            </button>
                            <button id="fullscreen-vehicle" class="text-sm">
                                <i class="bx bx-fullscreen"></i>
                                Fullscreen
                            </button>

                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="vehicleChart"></canvas>
                        </div>
                </div>

                <div class="chart-card">
                    <div class="chart-header">
                        <h2 class="text-xl font-semibold">Payment Methods</h2>
                    </div>
                    <div class="chart-container">
                        <canvas id="paymentChart"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="chart-header">
                        <h2 class="text-xl font-semibold">Time Slot Analysis</h2>
                        <div class="chart-controls">
                            <button id="fullscreen-timeslot" class="text-sm">
                                <i class="bx bx-fullscreen"></i>
                                Fullscreen
                            </button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="timeslotChart"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="chart-header">
                        <h2 class="text-xl font-semibold">Trainer Performance</h2>
                        <div class="chart-controls">
                            <select id="trainer-metric" class="chart-select text-sm">
                                <option value="revenue">Revenue Generated</option>
                                <option value="students">Total Students</option>
                                <!-- <option value="hours">Training Hours</option>
                                <option value="rating">Average Rating</option> -->
                            </select>
                            <button id="trainer-chart-type" class="text-sm">
                                <i class="bx bx-bar-chart"></i>
                                Toggle View
                            </button>
                            <button id="fullscreen-trainer" class="text-sm">
                                <i class="bx bx-fullscreen"></i>
                                Fullscreen
                            </button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="trainerChart"></canvas>
                    </div>
                </div>
            </div>


            <!-- Enhanced Analytics Section -->
            <div class="enhanced-analytics mt-8">
                <div class="section-title">
                    <h2 class="text-xl font-semibold mb-4">Detailed Business Insights</h2>
                </div>
                
                <!-- Business Performance Metrics -->
                <div class="metrics-grid">
                    <div class="metric-card">
                        <h3 class="metric-title">Operational Metrics</h3>
                        <div class="metric-content">
                            <div class="metric-item">
                                <span class="metric-label">Total Active Days</span>
                                <p class="metric-value" id="totalActiveDays">0</p>
                            </div>
                            <div class="metric-item">
                                <span class="metric-label">Average Daily Bookings</span>
                                <p class="metric-value" id="avgDailyBookings">0</p>
                            </div>
                            <div class="metric-item">
                                <span class="metric-label">Total Trainers</span>
                                <p class="metric-value" id="totalTrainers">0</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="metric-card">
                        <h3 class="metric-title">Payment Analysis</h3>
                        <div class="metric-content">
                            <div class="metric-item">
                                <span class="metric-label">Fully Paid Bookings</span>
                                <p class="metric-value" id="fullyPaidBookings">0</p>
                            </div>
                            <div class="metric-item">
                                <span class="metric-label">Partial Paid Bookings</span>
                                <p class="metric-value" id="partialPaidBookings">0</p>
                            </div>
                            <div class="metric-item">
                                <span class="metric-label">Average Pending Amount</span>
                                <p class="metric-value" id="avgPendingAmount">₹0</p>
                            </div>
                        </div>
                    </div>
                </div>

                <style>
                    .section-title h2 {
                        color: var(--text-primary);
                        position: relative;
                        padding-bottom: 0.5rem;
                    }
                    
                    .section-title h2::after {
                        content: '';
                        position: absolute;
                        left: 0;
                        bottom: 0;
                        width: 50px;
                        height: 3px;
                        background: var(--primary-color);
                        border-radius: 2px;
                    }

                    .metric-card {
                        background: linear-gradient(145deg, var(--card-background), #f8fafc);
                        border: 1px solid rgba(226, 232, 240, 0.8);
                        transition: all 0.3s ease;
                    }

                    .metric-card:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                    }

                    .metric-title {
                        color: var(--text-primary);
                        font-size: 1.25rem;
                        font-weight: 600;
                        margin-bottom: 1.5rem;
                        padding-bottom: 0.75rem;
                        border-bottom: 2px solid var(--border-color);
                    }

                    .metric-item {
                        background: white;
                        border: 1px solid var(--border-color);
                        transition: all 0.2s ease;
                        position: relative;
                        overflow: hidden;
                    }

                    .metric-item:hover {
                        transform: translateY(-1px);
                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
                        border-color: var(--primary-color);
                    }

                    .metric-label {
                        display: block;
                        color: var(--text-secondary);
                        font-size: 0.875rem;
                        margin-bottom: 0.5rem;
                    }

                    .metric-value {
                        color: var(--text-primary);
                        font-size: 1.5rem;
                        font-weight: 600;
                        line-height: 1.2;
                    }

                    @media (max-width: 640px) {
                        .metrics-grid {
                            gap: 1rem;
                        }

                        .metric-content {
                            grid-template-columns: 1fr;
                        }

                        .metric-title {
                            font-size: 1.125rem;
                            margin-bottom: 1rem;
                        }

                        .metric-value {
                            font-size: 1.25rem;
                        }
                    }

                    @media (min-width: 641px) and (max-width: 1024px) {
                        .metric-content {
                            grid-template-columns: repeat(2, 1fr);
                        }
                    }

                    @media (min-width: 1025px) {
                        .metrics-grid {
                            gap: 2rem;
                        }
                    }
                </style>

                <!-- Vehicle Insights -->
                <div class="insights-section" id="vehicleInsights">
                    <div class="insights-header">
                        <h3 class="insights-title">
                            <i class="fas fa-car-side"></i>
                            Vehicle Performance Insights
                        </h3>
                        <div class="insights-actions">
                            <button class="refresh-btn" onclick="refreshInsights('vehicle')">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="insights-grid" id="vehicleInsightsGrid"></div>
                </div>

                <!-- Time Slot Insights -->
                <div class="insights-section" id="timeslotInsights">
                    <div class="insights-header">
                        <h3 class="insights-title">
                            <i class="fas fa-clock"></i>
                            Time Slot Analysis
                        </h3>
                        <div class="insights-actions">
                            <button class="refresh-btn" onclick="refreshInsights('timeslot')">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="insights-grid" id="timeslotInsightsGrid"></div>
                </div>

                <!-- Payment Method Insights -->
                <div class="insights-section" id="paymentInsights">
                    <div class="insights-header">
                        <h3 class="insights-title">
                            <i class="fas fa-money-bill-wave"></i>
                            Payment Method Analysis
                        </h3>
                        <div class="insights-actions">
                            <button class="refresh-btn" onclick="refreshInsights('payment')">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="insights-grid" id="paymentInsightsGrid"></div>
                </div>

                <style>
                    .insights-section {
                        background: var(--card-background);
                        border-radius: var(--border-radius);
                        padding: var(--spacing-lg);
                        margin-top: var(--spacing-lg);
                        box-shadow: var(--box-shadow);
                        transition: all 0.3s ease;
                    }

                    .insights-section:hover {
                        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
                    }

                    .insights-header {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-bottom: var(--spacing-md);
                        padding-bottom: var(--spacing-sm);
                        border-bottom: 2px solid var(--border-color);
                    }

                    .insights-title {
                        color: var(--text-primary);
                        font-size: 1.25rem;
                        font-weight: 600;
                        display: flex;
                        align-items: center;
                        gap: 0.5rem;
                    }

                    .insights-title i {
                        color: var(--primary-color);
                        font-size: 1.1em;
                    }

                    .insights-actions {
                        display: flex;
                        gap: var(--spacing-sm);
                    }

                    .refresh-btn {
                        background: transparent;
                        border: none;
                        color: var(--text-secondary);
                        cursor: pointer;
                        padding: 0.5rem;
                        border-radius: var(--border-radius);
                        transition: all 0.2s ease;
                    }

                    .refresh-btn:hover {
                        color: var(--primary-color);
                        background: rgba(70, 171, 204, 0.1);
                    }

                    .refresh-btn i {
                        font-size: 1rem;
                    }

                    .insights-grid {
                        display: grid;
                        gap: var(--spacing-md);
                        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                    }

                    .insight-card {
                        background: linear-gradient(145deg, #ffffff, #f8fafc);
                        border: 1px solid var(--border-color);
                        border-radius: var(--border-radius);
                        padding: var(--spacing-md);
                        transition: all 0.3s ease;
                        position: relative;
                        overflow: hidden;
                    }

                    .insight-card::before {
                        content: '';
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 4px;
                        height: 100%;
                        background: var(--primary-color);
                        opacity: 0;
                        transition: opacity 0.3s ease;
                    }

                    .insight-card:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                    }

                    .insight-card:hover::before {
                        opacity: 1;
                    }

                    .insight-card h4 {
                        color: var(--text-primary);
                        font-size: 1.1rem;
                        font-weight: 600;
                        margin-bottom: var(--spacing-md);
                        padding-bottom: var(--spacing-sm);
                        border-bottom: 1px solid var(--border-color);
                    }

                    .insight-stat {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        padding: 0.5rem 0;
                        transition: all 0.2s ease;
                    }

                    .insight-stat:hover {
                        background: rgba(70, 171, 204, 0.05);
                        padding: 0.5rem;
                        margin: 0 -0.5rem;
                        border-radius: var(--border-radius);
                    }

                    .insight-stat span:first-child {
                        color: var(--text-secondary);
                        font-size: 0.875rem;
                    }

                    .insight-stat span:last-child {
                        color: var(--text-primary);
                        font-weight: 500;
                        font-size: 0.9rem;
                    }

                    @media (max-width: 768px) {
                        .insights-section {
                            padding: var(--spacing-md);
                        }

                        .insights-grid {
                            grid-template-columns: 1fr;
                        }

                        .insight-card {
                            margin-bottom: var(--spacing-sm);
                        }
                    }

                    @keyframes refresh-spin {
                        from { transform: rotate(0deg); }
                        to { transform: rotate(360deg); }
                    }

                    .refresh-btn.spinning i {
                        animation: refresh-spin 1s linear infinite;
                    }
                </style>

                <script>
                    function refreshInsights(type) {
                        const btn = event.currentTarget;
                        btn.classList.add('spinning');
                        
                        // Simulate refresh delay
                        setTimeout(() => {
                            updateCharts();
                            btn.classList.remove('spinning');
                        }, 1000);
                    }

                    // Enhance the existing updateInsights function
                    const originalUpdateInsights = window.updateInsights;
                    window.updateInsights = function(distributions) {
                        try {
                            // Call the original function
                            originalUpdateInsights(distributions);

                            // Add animation to new cards
                            const cards = document.querySelectorAll('.insight-card');
                            cards.forEach((card, index) => {
                                card.style.opacity = '0';
                                card.style.transform = 'translateY(20px)';
                                setTimeout(() => {
                                    card.style.transition = 'all 0.3s ease';
                                    card.style.opacity = '1';
                                    card.style.transform = 'translateY(0)';
                                }, index * 100);
                            });
                        } catch (error) {
                            console.error('Error in enhanced updateInsights:', error);
                        }
                    };
                </script>
            </div>

            <!-- Advanced Analytics Section -->
            <div class="advanced-analytics mt-8">
                <!-- Customer Insights -->
                <div class="analytics-section">
                    <h2 class="section-title">Customer Insights</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="insight-card">
                            <h3>Customer Acquisition</h3>
                            <div class="insight-content">
                                <div class="metric">
                                    <span>Last 30 Days</span>
                                    <p id="new-customers-30d">0</p>
                                </div>
                                <div class="metric">
                                    <span>Last 90 Days</span>
                                    <p id="new-customers-90d">0</p>
                                </div>
                            </div>
                        </div>
                        <div class="insight-card">
                            <h3>Training Statistics</h3>
                            <div class="insight-content">
                                <div class="metric">
                                    <span>Avg. Duration (days)</span>
                                    <p id="avg-training-duration">0</p>
                                </div>
                                <div class="metric">
                                    <span>New Licenses</span>
                                    <p id="new-licenses">0</p>
                                </div>
                                <div class="metric">
                                    <span>Existing Licenses</span>
                                    <p id="existing-licenses">0</p>
                                </div>
                            </div>
                        </div>
                        <div class="insight-card">
                            <h3>Geographic Reach</h3>
                            <div class="insight-content">
                                <div class="metric">
                                    <span>Unique Locations</span>
                                    <p id="unique-locations">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trainer Performance -->
                <div class="analytics-section mt-8">
                    <h2 class="section-title">Trainer Performance</h2>
                    <div class="overflow-x-auto">
                        <table class="trainer-performance-table">
                            <thead>
                                <tr>
                                    <th>Trainer Name</th>
                                    <th>Total Students</th>
                                    <th>Revenue</th>
                                    <th>Outstanding Dues</th>
                                    <th>Vehicle Types</th>
                                    <th>Time Slots</th>
                                    <th>Avg. Training Days</th>
                                </tr>
                            </thead>
                            <tbody id="trainer-performance-body">
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Payment Analysis -->
                <div class="analytics-section mt-8">
                    <h2 class="section-title">Payment Analysis</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="insight-card">
                            <h3>Payment Method Performance</h3>
                            <div class="chart-container">
                                <canvas id="payment-method-chart" width="400" height="300"></canvas>
                            </div>
                        </div>
                        <div class="insight-card">
                            <h3>Due Amount Aging</h3>
                            <div class="chart-container">
                                <canvas id="dues-aging-chart" width="400" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Time Slot Analysis -->
                <div class="analytics-section mt-8">
                    <h2 class="section-title">Time Slot Analysis</h2>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="insight-card">
                            <h3>Slot Efficiency</h3>
                            <div class="chart-container">
                                <canvas id="slot-efficiency-chart" width="400" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->

    <script>
        let revenueChart = null;
        let vehicleChart = null;
        let paymentChart = null;
        let timeslotChart = null;

        const chartColors = {
            revenue: {
                total: 'rgba(59, 130, 246, 0.5)',
                paid: 'rgba(16, 185, 129, 0.5)',
                due: 'rgba(239, 68, 68, 0.5)'
            },
            border: {
                total: 'rgb(59, 130, 246)',
                paid: 'rgb(16, 185, 129)',
                due: 'rgb(239, 68, 68)'
            }
        };

        function initializeCharts() {
            try {
                // Ensure Chart.js plugins are registered
                if (Chart.registry && !Chart.registry.getPlugin('zoom')) {
                    console.warn('Zoom plugin not registered. Attempting to register...');
                    try {
                        // If using Chart.js v3+
                        if (window.ChartZoom) {
                            Chart.register(window.ChartZoom);
                        }
                    } catch (e) {
                        console.error('Failed to register zoom plugin:', e);
                    }
                }
                
                // Get canvas elements
                const canvasElements = {
                    revenue: document.getElementById('revenueChart'),
                    vehicle: document.getElementById('vehicleChart'),
                    payment: document.getElementById('paymentChart'),
                    timeslot: document.getElementById('timeslotChart')
                };

                // Check if all canvas elements exist
                for (const [key, element] of Object.entries(canvasElements)) {
                    if (!element) {
                        throw new Error(`Canvas element ${key}Chart not found`);
                    }
                }

                // Get contexts with error handling
                const contexts = {};
                for (const [key, element] of Object.entries(canvasElements)) {
                    try {
                        contexts[key] = element.getContext('2d');
                        if (!contexts[key]) {
                            throw new Error(`Could not get 2D context for ${key}Chart`);
                        }
                    } catch (error) {
                        console.error(`Error getting context for ${key}Chart:`, error);
                        throw error;
                    }
                }

                // Initialize Revenue Chart
                revenueChart = new Chart(contexts.revenue, {
                    type: 'bar',
                    data: {
                        labels: [],
                        datasets: [
                            {
                                label: 'Total Amount',
                                data: [],
                                backgroundColor: chartColors.revenue.total,
                                borderColor: chartColors.border.total,
                                borderWidth: 1
                            },
                            {
                                label: 'Received Amount',
                                data: [],
                                backgroundColor: chartColors.revenue.paid,
                                borderColor: chartColors.border.paid,
                                borderWidth: 1
                            },
                            {
                                label: 'Due Amount',
                                data: [],
                                backgroundColor: chartColors.revenue.due,
                                borderColor: chartColors.border.due,
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            zoom: {
                                pan: {
                                    enabled: true,
                                    mode: 'xy',
                                    modifierKey: 'shift'
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
                                        enabled: false
                                    },
                                }
                            },
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Amount (₹)'
                                }
                            }
                        }
                    }
                });

                // Initialize Vehicle Distribution Chart
                vehicleChart = new Chart(contexts.vehicle, {
                    type: 'doughnut',
                    data: {
                        labels: [],
                        datasets: [{
                            data: [],
                            backgroundColor: [],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right'
                            }
                        }
                    }
                });

                // Initialize Payment Methods Chart
                paymentChart = new Chart(contexts.payment, {
                    type: 'pie',
                    data: {
                        labels: [],
                        datasets: [{
                            data: [],
                            backgroundColor: [],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right'
                            }
                        }
                    }
                });

                // Initialize Time Slot Chart
                timeslotChart = new Chart(contexts.timeslot, {
                    type: 'bar',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Bookings',
                            data: [],
                            backgroundColor: 'rgba(99, 102, 241, 0.5)',
                            borderColor: 'rgb(99, 102, 241)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Number of Bookings'
                                }
                            }
                        }
                    }
                });

            } catch (error) {
                console.error('Error initializing charts:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to initialize charts. Please refresh the page.'
                });
            }
        }

        async function updateCharts() {
            try {
                // Check if select elements exist
                const yearSelect = document.getElementById('select-year');
                const monthSelect = document.getElementById('select-month');
                
                if (!yearSelect || !monthSelect) {
                    throw new Error('Year or month select elements not found');
                }

                const year = yearSelect.value;
                const month = monthSelect.value;
                
                setLoadingState(true);
                
                // Validate that charts are initialized
                if (!revenueChart || !vehicleChart || !paymentChart || !timeslotChart) {
                    throw new Error('Charts not properly initialized');
                }

                const response = await fetch(`./api/chart-data.php?year=${year}&month=${month}`);
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Server response:', errorText);
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (!data) {
                    throw new Error('No data received from server');
                }

                if (!data.success) {
                    console.error('Server error details:', data.debug);
                    throw new Error(data.error || 'Invalid data received from server');
                }

                // Validate required data structures
                if (!data.revenue || !data.distributions || !data.stats || !data.advanced_analytics) {
                    throw new Error('Incomplete data structure received from server');
                }

                // Update charts with error handling
                try {
                    updateChartData(data);
                    updateStats(data.stats);
                    updateInsights(data.distributions);
                    updateAdvancedAnalytics(data.advanced_analytics);
                } catch (error) {
                    console.error('Error updating chart data:', error);
                    throw new Error('Failed to update charts with new data');
                }

            } catch (error) {
                console.error('Error in updateCharts:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Failed to update charts. Please try again.',
                    footer: '<a href="#">Contact support if the problem persists</a>'
                });
            } finally {
                setLoadingState(false);
            }
        }

        function updateStats(stats) {
            try {
                if (!stats) {
                    throw new Error('No stats data provided');
                }

                // Define all the stats elements we need to update
                const statElements = {
                    'totalAmount': { prefix: '₹', format: true },
                    'totalPaid': { prefix: '₹', format: true },
                    'totalDue': { prefix: '₹', format: true },
                    'collectionRate': { suffix: '%', format: false },
                    'totalCustomers': { format: true },
                    'avgSale': { prefix: '₹', format: true, key: 'averageSale' },
                    'monthlyGrowth': { suffix: '%', format: false },
                    'yearlyGrowth': { suffix: '%', format: false },
                    'totalActiveDays': { format: true },
                    'avgDailyBookings': { format: true },
                    'totalTrainers': { format: true },
                    'fullyPaidBookings': { format: true },
                    'partialPaidBookings': { format: true },
                    'avgPendingAmount': { prefix: '₹', format: true }
                };

                // Update each stat element
                for (const [id, config] of Object.entries(statElements)) {
                    const element = document.getElementById(id);
                    if (!element) {
                        console.warn(`Element with id '${id}' not found`);
                        continue;
                    }

                    let value = stats[config.key || id];
                    if (value === undefined || value === null) {
                        console.warn(`Stat '${config.key || id}' not found in data`);
                        value = 0;
                    }

                    // Format number if needed
                    if (config.format) {
                        value = Number(value).toLocaleString();
                    }

                    // Add prefix/suffix
                    let displayValue = '';
                    if (config.prefix) displayValue += config.prefix;
                    displayValue += value;
                    if (config.suffix) displayValue += config.suffix;

                    element.textContent = displayValue;
                }
            } catch (error) {
                console.error('Error updating stats:', error);
                throw new Error('Failed to update statistics');
            }
        }

        function updateInsights(distributions) {
            try {
                if (!distributions) {
                    throw new Error('No distributions data provided');
                }

                // Update Vehicle Insights
                const vehicleGrid = document.getElementById('vehicleInsightsGrid');
                if (vehicleGrid && distributions.vehicles) {
                    vehicleGrid.innerHTML = '';
                    distributions.vehicles.labels.forEach((label, index) => {
                        const insight = distributions.vehicles.insights[index];
                        if (!insight) return;

                        vehicleGrid.innerHTML += `
                            <div class="insight-card">
                                <h4>${label || 'Unknown'}</h4>
                                <div class="insight-stat">
                                    <span>Total Revenue:</span>
                                    <span>₹${Number(insight.total_revenue || 0).toLocaleString()}</span>
                                </div>
                                <div class="insight-stat">
                                    <span>Collection Rate:</span>
                                    <span>${insight.collection_rate || 0}%</span>
                                </div>
                                <div class="insight-stat">
                                    <span>Avg. Revenue:</span>
                                    <span>₹${Number(insight.avg_revenue || 0).toLocaleString()}</span>
                                </div>
                                <div class="insight-stat">
                                    <span>Pending Payments:</span>
                                    <span>${insight.pending_payments || 0}</span>
                                </div>
                            </div>
                        `;
                    });
                }

                // Update Time Slot Insights
                const timeslotGrid = document.getElementById('timeslotInsightsGrid');
                if (timeslotGrid && distributions.timeslots) {
                    timeslotGrid.innerHTML = '';
                    distributions.timeslots.labels.forEach((label, index) => {
                        const insight = distributions.timeslots.insights[index];
                        if (!insight) return;

                        timeslotGrid.innerHTML += `
                            <div class="insight-card">
                                <h4>${label || 'Unknown'}</h4>
                                <div class="insight-stat">
                                    <span>Utilization:</span>
                                    <span>${insight.slot_utilization || 0}%</span>
                                </div>
                                <div class="insight-stat">
                                    <span>Total Revenue:</span>
                                    <span>₹${Number(insight.total_revenue || 0).toLocaleString()}</span>
                                </div>
                                <div class="insight-stat">
                                    <span>Unique Trainers:</span>
                                    <span>${insight.unique_trainers || 0}</span>
                                </div>
                                <div class="insight-stat">
                                    <span>Avg. Revenue:</span>
                                    <span>₹${Number(insight.avg_revenue || 0).toLocaleString()}</span>
                                </div>
                            </div>
                        `;
                    });
                }

                // Update Payment Method Insights
                const paymentGrid = document.getElementById('paymentInsightsGrid');
                if (paymentGrid && distributions.payments) {
                    paymentGrid.innerHTML = '';
                    distributions.payments.labels.forEach((label, index) => {
                        const insight = distributions.payments.insights[index];
                        if (!insight) return;

                        paymentGrid.innerHTML += `
                            <div class="insight-card">
                                <h4>${label || 'Unknown'}</h4>
                                <div class="insight-stat">
                                    <span>Total Revenue:</span>
                                    <span>₹${Number(insight.total_revenue || 0).toLocaleString()}</span>
                                </div>
                                <div class="insight-stat">
                                    <span>Avg. Transaction:</span>
                                    <span>₹${Number(insight.avg_transaction || 0).toLocaleString()}</span>
                                </div>
                                <div class="insight-stat">
                                    <span>Transaction Range:</span>
                                    <span>₹${Number(insight.min_transaction || 0).toLocaleString()} - ₹${Number(insight.max_transaction || 0).toLocaleString()}</span>
                                </div>
                                <div class="insight-stat">
                                    <span>Unique Vehicles:</span>
                                    <span>${insight.unique_vehicles || 0}</span>
                                </div>
                            </div>
                        `;
                    });
                }
            } catch (error) {
                console.error('Error updating insights:', error);
                throw new Error('Failed to update insights');
            }
        }

        function updateAdvancedAnalytics(data) {
            if (!data) return;

            const elements = {
                'new-customers-30d': data?.key_metrics?.customer_acquisition?.last_30_days,
                'new-customers-90d': data?.key_metrics?.customer_acquisition?.last_90_days,
                'avg-training-duration': data?.key_metrics?.training_metrics?.avg_duration,
                'new-licenses': data?.key_metrics?.training_metrics?.new_vs_existing?.new,
                'existing-licenses': data?.key_metrics?.training_metrics?.new_vs_existing?.existing,
                'unique-locations': data?.key_metrics?.geographic_reach
            };

            for (const [id, value] of Object.entries(elements)) {
                const element = document.getElementById(id);
                if (element) {
                    element.textContent = value || '0';
                }
            }

            // Update trainer performance table
            updateTrainerTable(data?.trainer_performance);
            updateSlotEfficiencyTable(data?.timeslot_efficiency);
            updateChartElements(data);
        }

        function updateTrainerTable(trainers) {
            const trainerBody = document.getElementById('trainer-performance-body');
            if (!trainerBody || !trainers) return;

            trainerBody.innerHTML = trainers.map(trainer => `
                <tr>
                    <td>${trainer.trainername || 'N/A'}</td>
                    <td>${trainer.total_students || '0'}</td>
                    <td>₹${Number(trainer.total_revenue || 0).toLocaleString()}</td>
                    <td>₹${Number(trainer.total_dues || 0).toLocaleString()}</td>
                    <td>${trainer.vehicle_types_handled || 'N/A'}</td>
                    <td>${trainer.time_slots_covered || 'N/A'}</td>
                    <td>${Number(trainer.avg_training_days || 0).toFixed(1)}</td>
                </tr>
            `).join('');
        }

        function updateSlotEfficiencyTable(slots) {
            const slotBody = document.getElementById('slot-efficiency-body');
            if (!slotBody || !slots) return;

            slotBody.innerHTML = slots.map(slot => `
                <tr>
                    <td>${slot.timeslot || 'N/A'}</td>
                    <td>${slot.booking_count || '0'}</td>
                    <td>₹${Number(slot.revenue || 0).toLocaleString()}</td>
                    <td>${slot.trainers_count || '0'}</td>
                    <td>${slot.vehicle_types || 'N/A'}</td>
                </tr>
            `).join('');
        }

        function updateChartElements(data) {
            try {
                if (!data) return;
                
                // Update Payment Method Chart
                if (data.payment_analytics) {
                    updatePaymentMethodChart(data.payment_analytics);
                }
                
                // Update Dues Aging Chart
                if (data.dues_aging) {
                    updateDuesAgingChart(data.dues_aging);
                }
                
                // Update Slot Efficiency Chart
                if (data.timeslot_efficiency) {
                    updateSlotEfficiencyChart(data.timeslot_efficiency);
                }
            } catch (error) {
                console.error('Error updating chart elements:', error);
            }
        }

        function updatePaymentMethodChart(paymentAnalytics) {
            try {
                const chartCanvas = document.getElementById('payment-method-chart');
                if (!chartCanvas) {
                    console.error('Payment method chart canvas not found');
                    return;
                }

                // Check if canvas is actually a canvas element
                if (!(chartCanvas instanceof HTMLCanvasElement)) {
                    console.error('Element is not a canvas element');
                    return;
                }

                const ctx = chartCanvas.getContext('2d');
                if (!ctx) {
                    console.error('Could not get 2D context for payment method chart');
                    return;
                }

                const paymentMethodData = {
                    labels: paymentAnalytics.map(p => p.payment_method || 'Unknown'),
                    datasets: [{
                        label: 'Transaction Count',
                        data: paymentAnalytics.map(p => p.transaction_count || 0),
                        backgroundColor: generateColors(paymentAnalytics.length)
                    }]
                };

                if (window.paymentMethodChart) {
                    window.paymentMethodChart.destroy();
                }

                window.paymentMethodChart = new Chart(ctx, {
                    type: 'pie',
                    data: paymentMethodData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right'
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error updating payment method chart:', error);
            }
        }

        function updateDuesAgingChart(duesAging) {
            try {
                const chartCanvas = document.getElementById('dues-aging-chart');
                if (!chartCanvas) {
                    console.error('Dues aging chart canvas not found');
                    return;
                }

                // Check if canvas is actually a canvas element
                if (!(chartCanvas instanceof HTMLCanvasElement)) {
                    console.error('Element is not a canvas element');
                    return;
                }

                const ctx = chartCanvas.getContext('2d');
                if (!ctx) {
                    console.error('Could not get 2D context for dues aging chart');
                    return;
                }

                const duesAgingData = {
                    labels: duesAging.map(d => d.aging_period || 'Unknown'),
                    datasets: [{
                        label: 'Total Dues',
                        data: duesAging.map(d => d.total_dues || 0),
                        backgroundColor: 'rgba(239, 68, 68, 0.5)',
                        borderColor: 'rgb(239, 68, 68)',
                                        borderWidth: 1
                    }]
                };

                if (window.duesAgingChart) {
                    window.duesAgingChart.destroy();
                }

                window.duesAgingChart = new Chart(ctx, {
                    type: 'bar',
                    data: duesAgingData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Amount (₹)'
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error updating dues aging chart:', error);
            }
        }

        function updateSlotEfficiencyChart(slotEfficiency) {
            try {
                const chartCanvas = document.getElementById('slot-efficiency-chart');
                if (!chartCanvas) {
                    console.error('Slot efficiency chart canvas not found');
                    return;
                }

                // Check if canvas is actually a canvas element
                if (!(chartCanvas instanceof HTMLCanvasElement)) {
                    console.error('Element is not a canvas element');
                    return;
                }

                const ctx = chartCanvas.getContext('2d');
                if (!ctx) {
                    console.error('Could not get 2D context for slot efficiency chart');
                    return;
                }

                const slotEfficiencyData = {
                    labels: slotEfficiency.map(s => s.timeslot || 'Unknown'),
                    datasets: [{
                        label: 'Booking Count',
                        data: slotEfficiency.map(s => s.booking_count || 0),
                        backgroundColor: 'rgba(99, 102, 241, 0.5)',
                        borderColor: 'rgb(99, 102, 241)',
                        borderWidth: 1
                    }]
                };

                if (window.slotEfficiencyChart) {
                    window.slotEfficiencyChart.destroy();
                }

                window.slotEfficiencyChart = new Chart(ctx, {
                    type: 'bar',
                    data: slotEfficiencyData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Number of Bookings'
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error updating slot efficiency chart:', error);
            }
        }

        function updateChartData(data) {
            // Update Revenue Chart
            revenueChart.data.labels = data.months;
            revenueChart.data.datasets[0].data = data.revenue.total;
            revenueChart.data.datasets[1].data = data.revenue.paid;
            revenueChart.data.datasets[2].data = data.revenue.due;
            revenueChart.update();

            // Update Vehicle Distribution Chart
            vehicleChart.data.labels = data.distributions.vehicles.labels;
            vehicleChart.data.datasets[0].data = data.distributions.vehicles.values;
            vehicleChart.data.datasets[0].backgroundColor = generateColors(data.distributions.vehicles.labels.length);
            vehicleChart.update();

            // Update Payment Methods Chart
            paymentChart.data.labels = data.distributions.payments.labels;
            paymentChart.data.datasets[0].data = data.distributions.payments.values;
            paymentChart.data.datasets[0].backgroundColor = generateColors(data.distributions.payments.labels.length);
            paymentChart.update();

            // Update Time Slot Chart
            timeslotChart.data.labels = data.distributions.timeslots.labels;
            timeslotChart.data.datasets[0].data = data.distributions.timeslots.values;
            timeslotChart.update();
        }

        function generateColors(count) {
            const colors = [];
            for (let i = 0; i < count; i++) {
                colors.push(`hsl(${(i * 360) / count}, 70%, 60%)`);
            }
            return colors;
        }

        // Event Listeners
        document.addEventListener('DOMContentLoaded', function() {
            try {
                initializeCharts();
                updateCharts();

                // Add event listeners with error handling
                const yearSelect = document.getElementById('select-year');
                const monthSelect = document.getElementById('select-month');
                const chartTypeBtn = document.getElementById('change-chart-type');
                const distributionViewBtn = document.getElementById('change-distribution-view');
                const resetZoomBtn = document.getElementById('reset-zoom-revenue');
                const fullscreenBtn = document.getElementById('fullscreen-revenue');

                if (yearSelect) yearSelect.addEventListener('change', updateCharts);
                if (monthSelect) monthSelect.addEventListener('change', updateCharts);
                
                if (chartTypeBtn) {
                    chartTypeBtn.addEventListener('click', function() {
                        if (!revenueChart) return;
                        const currentType = revenueChart.config.type;
                        revenueChart.config.type = currentType === 'bar' ? 'line' : 'bar';
                        revenueChart.update();
                    });
                }

                if (distributionViewBtn) {
                    distributionViewBtn.addEventListener('click', function() {
                        if (!vehicleChart) return;
                        const currentType = vehicleChart.config.type;
                        vehicleChart.config.type = currentType === 'doughnut' ? 'bar' : 'doughnut';
                        
                        vehicleChart.options.plugins.legend.position = vehicleChart.config.type === 'bar' ? 'top' : 'right';
                        vehicleChart.options.scales = vehicleChart.config.type === 'bar' ? {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Number of Customers'
                                }
                            }
                        } : {};
                        
                        vehicleChart.update();
                    });
                }

                // Add reset zoom functionality
                if (resetZoomBtn) {
                    resetZoomBtn.addEventListener('click', function() {
                        if (!revenueChart) return;
                        if (revenueChart.resetZoom) {
                            revenueChart.resetZoom();
                        }
                    });
                }

                // Add fullscreen functionality
                if (fullscreenBtn) {
                    fullscreenBtn.addEventListener('click', function() {
                        openFullscreenChart();
                    });
                }

                // Setup fullscreen modal functionality
                setupFullscreenModal();

                window.addEventListener('resize', handleResize);
            } catch (error) {
                console.error('Error in DOMContentLoaded:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to initialize the page. Please refresh.'
                });
            }
        });

        // Add loading state handler
        function setLoadingState(isLoading) {
            const containers = document.querySelectorAll('.chart-container, .insights-section');
            containers.forEach(container => {
                if (isLoading) {
                    container.classList.add('loading');
                    container.style.opacity = '0.5';
                } else {
                    container.classList.remove('loading');
                    container.style.opacity = '1';
                }
            });
        }

        // Add resize handler for charts
        function handleResize() {
            if (revenueChart) revenueChart.resize();
            if (vehicleChart) vehicleChart.resize();
            if (paymentChart) paymentChart.resize();
            if (timeslotChart) timeslotChart.resize();
            if (window.paymentMethodChart) window.paymentMethodChart.resize();
            if (window.duesAgingChart) window.duesAgingChart.resize();
            if (window.slotEfficiencyChart) window.slotEfficiencyChart.resize();
        }

        // Fullscreen chart functionality
        let fullscreenChart = null;

        function setupFullscreenModal() {
            const modal = document.getElementById('fullscreen-modal');
            const closeBtn = document.getElementById('fullscreen-close');
            const chartTypeBtn = document.getElementById('fullscreen-chart-type');
            const resetZoomBtn = document.getElementById('fullscreen-reset-zoom');
            const togglePointsBtn = document.getElementById('fullscreen-toggle-points');
            const toggleFillBtn = document.getElementById('fullscreen-toggle-fill');
            const stackDataBtn = document.getElementById('fullscreen-stack-data');
            const lineStyleSelect = document.getElementById('fullscreen-line-style');
            const chartStyleSelect = document.getElementById('fullscreen-chart-style');
            const dataLabelsBtn = document.getElementById('fullscreen-data-labels');
            const gradientBtn = document.getElementById('fullscreen-gradient');
            const animationBtn = document.getElementById('fullscreen-animation');

            if (closeBtn) {
                closeBtn.addEventListener('click', closeFullscreenChart);
            }

            if (chartStyleSelect) {
                chartStyleSelect.addEventListener('change', function() {
                    if (!fullscreenChart) return;
                    const newType = this.value;
                    const previousType = fullscreenChart.config.type;
                    
                    // Store original settings before any changes
                    const originalSettings = fullscreenChart.data.datasets.map(dataset => ({
                        backgroundColor: dataset.backgroundColor,
                        borderColor: dataset.borderColor,
                        fill: dataset.fill,
                        borderWidth: dataset.borderWidth,
                        pointRadius: dataset.pointRadius,
                        pointHoverRadius: dataset.pointHoverRadius,
                        tension: dataset.tension,
                        stepped: dataset.stepped
                    }));

                    // Special handling for area chart
                    if (newType === 'area') {
                        fullscreenChart.config.type = 'line';
                        fullscreenChart.data.datasets.forEach(dataset => {
                            dataset.fill = 'origin';
                        });
                    } else {
                        fullscreenChart.config.type = newType;
                        fullscreenChart.data.datasets.forEach(dataset => {
                            dataset.fill = false;
                        });
                    }

                    // Special handling for scatter plot
                    if (newType === 'scatter') {
                        fullscreenChart.data.datasets.forEach(dataset => {
                            dataset.showLine = false;
                            dataset.pointRadius = 6;
                            dataset.pointHoverRadius = 8;
                        });
                    }

                    // Handle radar chart type change
                    if (newType === 'radar' || previousType === 'radar') {
                        handleRadarChartTypeChange(newType === 'radar', originalSettings);
                    }

                    // Update visibility of controls based on chart type
                    const lineStyleSelect = document.getElementById('fullscreen-line-style');
                    const toggleFillBtn = document.getElementById('fullscreen-toggle-fill');
                    const stackDataBtn = document.getElementById('fullscreen-stack-data');
                    const togglePointsBtn = document.getElementById('fullscreen-toggle-points');

                    if (lineStyleSelect) {
                        lineStyleSelect.style.display = newType === 'line' || newType === 'area' ? 'block' : 'none';
                    }
                    if (toggleFillBtn) {
                        toggleFillBtn.style.display = newType === 'radar' ? 'none' : 'block';
                    }
                    if (stackDataBtn) {
                        stackDataBtn.style.display = newType === 'bar' ? 'block' : 'none';
                    }
                    if (togglePointsBtn) {
                        togglePointsBtn.style.display = newType === 'line' || newType === 'area' || newType === 'radar' || newType === 'scatter' ? 'block' : 'none';
                    }

                    updateChartOptions();
                    fullscreenChart.update();
                });
            }

            if (dataLabelsBtn) {
                dataLabelsBtn.addEventListener('click', function() {
                    if (!fullscreenChart) return;
                    const hasLabels = fullscreenChart.options.plugins.datalabels?.display;
                    
                    fullscreenChart.options.plugins.datalabels = {
                        display: !hasLabels,
                        color: 'white',
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        borderRadius: 4,
                        padding: 4,
                        font: {
                            weight: 'bold'
                        },
                        formatter: (value) => {
                            if (typeof value === 'number') {
                                return '₹' + value.toLocaleString();
                            }
                            return value;
                        }
                    };
                    
                    fullscreenChart.update();
                });
            }

            if (gradientBtn) {
                gradientBtn.addEventListener('click', function() {
                    if (!fullscreenChart) return;
                    const ctx = fullscreenChart.ctx;
                    
                    fullscreenChart.data.datasets.forEach((dataset, index) => {
                        const color = dataset.borderColor || dataset.backgroundColor;
                        const hasGradient = typeof dataset.backgroundColor === 'object';
                        
                        if (!hasGradient) {
                            const gradient = ctx.createLinearGradient(0, 0, 0, fullscreenChart.height);
                            gradient.addColorStop(0, color);
                            gradient.addColorStop(1, 'rgba(255, 255, 255, 0.1)');
                            dataset.backgroundColor = gradient;
                        } else {
                            dataset.backgroundColor = color;
                        }
                    });
                    
                    fullscreenChart.update();
                });
            }

            if (animationBtn) {
                animationBtn.addEventListener('click', function() {
                    if (!fullscreenChart) return;
                    
                    // Store current data
                    const currentData = JSON.parse(JSON.stringify(fullscreenChart.data.datasets));
                    
                    // Reset data to zero
                    fullscreenChart.data.datasets.forEach(dataset => {
                        dataset.data = dataset.data.map(() => 0);
                    });
                    fullscreenChart.update('none');
                    
                    // Animate to actual values
                    setTimeout(() => {
                        fullscreenChart.data.datasets = currentData;
                        fullscreenChart.options.animation = {
                            duration: 1500,
                            easing: 'easeInOutQuart'
                        };
                        fullscreenChart.update();
                    }, 50);
                });
            }

            // Existing event listeners...
            if (chartTypeBtn) {
                chartTypeBtn.addEventListener('click', function() {
                    if (!fullscreenChart) return;
                    const currentType = fullscreenChart.config.type;
                    fullscreenChart.config.type = currentType === 'bar' ? 'line' : 'bar';
                    updateChartOptions();
                    fullscreenChart.update();
                });
            }

            // ... (keep existing event listeners)

            if (resetZoomBtn) {
                resetZoomBtn.addEventListener('click', function() {
                    if (!fullscreenChart || !fullscreenChart.resetZoom) return;
                    fullscreenChart.resetZoom();
                });
            }

            if (togglePointsBtn) {
                togglePointsBtn.addEventListener('click', function() {
                    if (!fullscreenChart) return;
                    const datasets = fullscreenChart.data.datasets;
                    datasets.forEach(dataset => {
                        dataset.pointRadius = dataset.pointRadius === 0 ? 4 : 0;
                    });
                    fullscreenChart.update();
                });
            }

            if (toggleFillBtn) {
                toggleFillBtn.addEventListener('click', function() {
                    if (!fullscreenChart) return;
                    const datasets = fullscreenChart.data.datasets;
                    datasets.forEach(dataset => {
                        if (fullscreenChart.config.type === 'line') {
                            dataset.fill = dataset.fill ? false : 'origin';
                        } else {
                            const currentOpacity = dataset.backgroundColor.includes('rgba') ? 
                                parseFloat(dataset.backgroundColor.split(',')[3]) : 1;
                            dataset.backgroundColor = dataset.backgroundColor.replace(
                                /[\d.]+\)$/,
                                `${currentOpacity === 1 ? 0.5 : 1})`
                            );
                        }
                    });
                    fullscreenChart.update();
                });
            }

            if (stackDataBtn) {
                stackDataBtn.addEventListener('click', function() {
                    if (!fullscreenChart) return;
                    const isStacked = fullscreenChart.options.scales.y.stacked;
                    fullscreenChart.options.scales.x.stacked = !isStacked;
                    fullscreenChart.options.scales.y.stacked = !isStacked;
                    fullscreenChart.update();
                });
            }

            if (lineStyleSelect) {
                lineStyleSelect.addEventListener('change', function() {
                    if (!fullscreenChart) return;
                    const datasets = fullscreenChart.data.datasets;
                    datasets.forEach(dataset => {
                        switch (this.value) {
                            case 'stepped':
                                dataset.stepped = true;
                                dataset.tension = 0;
                                break;
                            case 'curved':
                                dataset.stepped = false;
                                dataset.tension = 0.4;
                                break;
                            default: // linear
                                dataset.stepped = false;
                                dataset.tension = 0;
                                break;
                        }
                    });
                    fullscreenChart.update();
                });
            }

            // Close on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.classList.contains('show')) {
                    closeFullscreenChart();
                }
            });
        }

        function updateChartOptions() {
            if (!fullscreenChart) return;

            const isLine = fullscreenChart.config.type === 'line';
            const lineStyleSelect = document.getElementById('fullscreen-line-style');
            const toggleFillBtn = document.getElementById('fullscreen-toggle-fill');

            if (lineStyleSelect) {
                lineStyleSelect.style.display = isLine ? 'block' : 'none';
            }

            if (toggleFillBtn) {
                toggleFillBtn.querySelector('i').className = isLine ? 
                    'bx bx-paint' : 'bx bx-paint-roll';
            }

            // Reset certain options when switching types
            fullscreenChart.data.datasets.forEach(dataset => {
                if (isLine) {
                    dataset.tension = 0;
                    dataset.stepped = false;
                    dataset.fill = false;
                    dataset.pointRadius = 4;
                } else {
                    dataset.backgroundColor = dataset.backgroundColor.replace(
                        /[\d.]+\)$/,
                        '1)'
                    );
                }
            });
        }

        function openFullscreenChart() {
            const modal = document.getElementById('fullscreen-modal');
            const canvas = document.getElementById('fullscreen-chart');
            
            if (!modal || !canvas || !revenueChart) return;
            
            modal.classList.add('show');
            
            // Create a new chart in fullscreen mode with the same data
            if (fullscreenChart) {
                fullscreenChart.destroy();
            }
            
            const ctx = canvas.getContext('2d');
            
            // Initialize datasets with enhanced options
            const enhancedData = JSON.parse(JSON.stringify(revenueChart.data));
            enhancedData.datasets = enhancedData.datasets.map(dataset => ({
                ...dataset,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointStyle: 'circle',
                tension: 0,
                fill: false
            }));
            
            fullscreenChart = new Chart(ctx, {
                type: revenueChart.config.type,
                data: enhancedData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        zoom: {
                            pan: {
                                enabled: true,
                                mode: 'xy',
                                modifierKey: 'shift'
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
                                    backgroundColor: 'rgba(70, 171, 204, 0.2)',
                                    borderColor: 'rgba(70, 171, 204, 0.4)',
                                    borderWidth: 1
                                }
                            }
                        },
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                padding: 15
                            }
                        },
                        title: {
                            display: false
                        },
                        datalabels: {
                            display: false,
                            color: 'white',
                            backgroundColor: 'rgba(0, 0, 0, 0.7)',
                            borderRadius: 4,
                            padding: 4,
                            font: {
                                weight: 'bold'
                            },
                            formatter: (value) => {
                                if (typeof value === 'number') {
                                    return '₹' + value.toLocaleString();
                                }
                                return value;
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += '₹' + context.parsed.y.toLocaleString();
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked: false,
                            grid: {
                                display: true,
                                drawBorder: true,
                                drawOnChartArea: true,
                                drawTicks: true,
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 0
                            }
                        },
                        y: {
                            stacked: false,
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Amount (₹)',
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            },
                            grid: {
                                display: true,
                                drawBorder: true,
                                drawOnChartArea: true,
                                drawTicks: true,
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return '₹' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    animations: {
                        tension: {
                            duration: 1000,
                            easing: 'linear'
                        },
                        numbers: {
                            type: 'number',
                            duration: 500,
                            delay: 0,
                            loop: false
                        }
                    },
                    transitions: {
                        active: {
                            animation: {
                                duration: 400
                            }
                        }
                    },
                    elements: {
                        point: {
                            radius: 4,
                            hoverRadius: 6,
                            borderWidth: 2,
                            hoverBorderWidth: 2
                        },
                        line: {
                            tension: 0
                        },
                        bar: {
                            borderWidth: 1,
                            borderRadius: 4
                        }
                    },
                    layout: {
                        padding: {
                            top: 10,
                            right: 10,
                            bottom: 10,
                            left: 10
                        }
                    }
                }
            });
        }

        function closeFullscreenChart() {
            const modal = document.getElementById('fullscreen-modal');
            if (!modal) return;
            
            modal.classList.remove('show');
            
            // Destroy the fullscreen chart to free up resources
            if (fullscreenChart) {
                setTimeout(() => {
                    fullscreenChart.destroy();
                    fullscreenChart = null;
                }, 300);
            }
        }

        // Add new function to handle radar chart type changes
        function handleRadarChartTypeChange(isRadar, originalSettings = null) {
            if (!fullscreenChart) return;

            if (isRadar) {
                // Configure for radar chart
                fullscreenChart.data.datasets.forEach((dataset, index) => {
                    const color = dataset.borderColor;
                    dataset.backgroundColor = color.replace('rgb', 'rgba').replace(')', ', 0.2)');
                    dataset.pointBackgroundColor = color;
                    dataset.pointBorderColor = '#fff';
                    dataset.pointHoverBackgroundColor = '#fff';
                    dataset.pointHoverBorderColor = color;
                    dataset.borderWidth = 2;
                    dataset.pointRadius = 3;
                    dataset.pointHoverRadius = 5;
                });

                fullscreenChart.options.scales = {
                    r: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => '₹' + value.toLocaleString()
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        angleLines: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        pointLabels: {
                            font: {
                                size: 12
                            }
                        }
                    }
                };
            } else if (originalSettings) {
                // Restore original settings when switching from radar
                fullscreenChart.data.datasets.forEach((dataset, index) => {
                    dataset.backgroundColor = originalSettings[index].backgroundColor;
                    dataset.borderColor = originalSettings[index].borderColor;
                    dataset.fill = originalSettings[index].fill;
                    delete dataset.pointBackgroundColor;
                    delete dataset.pointBorderColor;
                    delete dataset.pointHoverBackgroundColor;
                    delete dataset.pointHoverBorderColor;
                });

                // Restore default scales
                fullscreenChart.options.scales = {
                    x: {
                        stacked: false,
                        grid: {
                            display: true,
                            drawBorder: true,
                            drawOnChartArea: true,
                            drawTicks: true,
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 0
                        }
                    },
                    y: {
                        stacked: false,
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Amount (₹)',
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            display: true,
                            drawBorder: true,
                            drawOnChartArea: true,
                            drawTicks: true,
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            callback: value => '₹' + value.toLocaleString()
                        }
                    }
                };
            }
        }
    </script>

    <script src="../../js/sweetalert.js"></script>
    <script src="../../js/toggleSideBar.js"></script>
    <script src="../../js/script.js"></script>
    <script src="../../js/hideSideBar.js"></script>

    <!-- Mobile Overlay Script -->
    <script>
        function checkScreenSize() {
            const overlay = document.getElementById('mobile-overlay');
            if (window.innerWidth < 768) {
                overlay.style.display = 'block';
                setTimeout(() => {
                    overlay.classList.add('show');
                }, 10);
            } else {
                overlay.classList.remove('show');
                setTimeout(() => {
                    overlay.style.display = 'none';
                }, 300);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            checkScreenSize();
            
            const overlay = document.getElementById('mobile-overlay');
            const continueBtn = document.getElementById('continue-anyway');
            
            if (continueBtn) {
                continueBtn.addEventListener('click', function() {
                    overlay.classList.remove('show');
                    setTimeout(() => {
                        overlay.style.display = 'none';
                    }, 300);
                });
            }
            
            // Check screen size on resize
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(checkScreenSize, 250);
            });
        });
    </script>

    <!-- Fullscreen Modal -->
    <div id="fullscreen-modal" class="fullscreen-modal">
        <button id="fullscreen-close" class="fullscreen-close">
            <i class="bx bx-x"></i>
        </button>
        <div class="fullscreen-chart-container">
            <canvas id="fullscreen-chart"></canvas>
        </div>
        <div class="fullscreen-controls">
            <button id="fullscreen-chart-type">
                <i class="bx bx-bar-chart"></i>
                Change Chart Type
            </button>
            <button id="fullscreen-toggle-points">
                <i class="bx bx-radio-circle"></i>
                Toggle Points
            </button>
            <button id="fullscreen-toggle-fill">
                <i class="bx bx-paint"></i>
                Toggle Fill
            </button>
            <button id="fullscreen-stack-data">
                <i class="bx bx-layer"></i>
                Stack Data
            </button>
            <select id="fullscreen-line-style" class="chart-select">
                <option value="linear">Linear</option>
                <option value="stepped">Stepped</option>
                <option value="curved">Curved</option>
            </select>
            <select id="fullscreen-chart-style" class="chart-select">
                <option value="bar">Bar</option>
                <option value="line">Line</option>
                <option value="area">Area</option>
                <option value="scatter">Scatter</option>
                <option value="radar">Radar</option>
            </select>
            <button id="fullscreen-data-labels">
                <i class="bx bx-tag"></i>
                Data Labels
            </button>
            <button id="fullscreen-gradient">
                <i class="bx bx-paint-roll"></i>
                Toggle Gradient
            </button>
            <button id="fullscreen-animation">
                <i class="bx bx-play"></i>
                Animate
            </button>
        </div>
    </div>

    <!-- Vehicle Fullscreen Modal -->
    <div id="vehicle-fullscreen-modal" class="fullscreen-modal">
        <button id="vehicle-fullscreen-close" class="fullscreen-close">
            <i class="bx bx-x"></i>
        </button>
        <div class="fullscreen-chart-container">
            <canvas id="vehicle-fullscreen-chart"></canvas>
        </div>
        <div class="fullscreen-controls">
            <button id="vehicle-fullscreen-chart-type">
                <i class="bx bx-pie-chart"></i>
                Toggle Chart Type
            </button>
            <button id="vehicle-fullscreen-data-labels">
                <i class="bx bx-tag"></i>
                Data Labels
            </button>
            <button id="vehicle-fullscreen-gradient">
                <i class="bx bx-paint-roll"></i>
                Toggle Gradient
            </button>
            <button id="vehicle-fullscreen-animation">
                <i class="bx bx-play"></i>
                Animate
            </button>
        </div>
    </div>

    <script>
// ... existing code ...

        let vehicleFullscreenChart = null;

        function openVehicleFullscreenChart() {
            const modal = document.getElementById('vehicle-fullscreen-modal');
            const canvas = document.getElementById('vehicle-fullscreen-chart');
            
            if (!modal || !canvas || !vehicleChart) return;
            
            modal.classList.add('show');
            
            // Create a new chart in fullscreen mode with the same data
            if (vehicleFullscreenChart) {
                vehicleFullscreenChart.destroy();
            }
            
            const ctx = canvas.getContext('2d');
            
            // Clone the data and configuration
            const enhancedData = JSON.parse(JSON.stringify(vehicleChart.data));
            
            vehicleFullscreenChart = new Chart(ctx, {
                type: vehicleChart.config.type,
                data: enhancedData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: vehicleChart.config.type === 'bar' ? 'top' : 'right',
                            labels: {
                                font: {
                                    size: 14
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += context.parsed.y || context.parsed;
                                    return label;
                                }
                            }
                        }
                    },
                    scales: vehicleChart.config.type === 'bar' ? {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Customers',
                                font: {
                                    size: 14
                                }
                            }
                        }
                    } : {}
                }
            });
        }

        function closeVehicleFullscreenChart() {
            const modal = document.getElementById('vehicle-fullscreen-modal');
            if (!modal) return;
            
            modal.classList.remove('show');
            
            // Destroy the fullscreen chart to free up resources
            if (vehicleFullscreenChart) {
                setTimeout(() => {
                    vehicleFullscreenChart.destroy();
                    vehicleFullscreenChart = null;
                }, 300);
            }
        }

        function setupVehicleFullscreenModal() {
            const modal = document.getElementById('vehicle-fullscreen-modal');
            const closeBtn = document.getElementById('vehicle-fullscreen-close');
            const chartTypeBtn = document.getElementById('vehicle-fullscreen-chart-type');
            const dataLabelsBtn = document.getElementById('vehicle-fullscreen-data-labels');
            const gradientBtn = document.getElementById('vehicle-fullscreen-gradient');
            const animationBtn = document.getElementById('vehicle-fullscreen-animation');

            if (closeBtn) {
                closeBtn.addEventListener('click', closeVehicleFullscreenChart);
            }

            if (chartTypeBtn) {
                chartTypeBtn.addEventListener('click', function() {
                    if (!vehicleFullscreenChart) return;
                    const currentType = vehicleFullscreenChart.config.type;
                    vehicleFullscreenChart.config.type = currentType === 'doughnut' ? 'bar' : 'doughnut';
                    
                    vehicleFullscreenChart.options.plugins.legend.position = 
                        vehicleFullscreenChart.config.type === 'bar' ? 'top' : 'right';
                    
                    vehicleFullscreenChart.options.scales = 
                        vehicleFullscreenChart.config.type === 'bar' ? {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Number of Customers',
                                    font: {
                                        size: 14
                                    }
                                }
                            }
                        } : {};
                    
                    vehicleFullscreenChart.update();
                    
                    // Update the main chart type as well
                    vehicleChart.config.type = vehicleFullscreenChart.config.type;
                    vehicleChart.options.plugins.legend.position = 
                        vehicleChart.config.type === 'bar' ? 'top' : 'right';
                    vehicleChart.options.scales = vehicleChart.config.type === 'bar' ? {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Customers'
                            }
                        }
                    } : {};
                    vehicleChart.update();
                });
            }

            if (dataLabelsBtn) {
                dataLabelsBtn.addEventListener('click', function() {
                    if (!vehicleFullscreenChart) return;
                    const hasLabels = vehicleFullscreenChart.options.plugins.datalabels?.display;
                    
                    vehicleFullscreenChart.options.plugins.datalabels = {
                        display: !hasLabels,
                        color: 'white',
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        borderRadius: 4,
                        padding: 4,
                        font: {
                            weight: 'bold'
                        },
                        formatter: (value) => value
                    };
                    
                    vehicleFullscreenChart.update();
                });
            }

            if (gradientBtn) {
                gradientBtn.addEventListener('click', function() {
                    if (!vehicleFullscreenChart) return;
                    const ctx = vehicleFullscreenChart.ctx;
                    const data = vehicleFullscreenChart.data;
                    
                    data.datasets.forEach((dataset, index) => {
                        const colors = dataset.backgroundColor;
                        const hasGradient = typeof colors[0] === 'object';
                        
                        if (!hasGradient) {
                            dataset.backgroundColor = colors.map(color => {
                                const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                                gradient.addColorStop(0, color);
                                gradient.addColorStop(1, 'rgba(255, 255, 255, 0.2)');
                                return gradient;
                            });
                        } else {
                            dataset.backgroundColor = generateColors(data.labels.length);
                        }
                    });
                    
                    vehicleFullscreenChart.update();
                });
            }

            if (animationBtn) {
                animationBtn.addEventListener('click', function() {
                    if (!vehicleFullscreenChart) return;
                    
                    const currentData = JSON.parse(JSON.stringify(vehicleFullscreenChart.data));
                    
                    vehicleFullscreenChart.data.datasets.forEach(dataset => {
                        dataset.data = dataset.data.map(() => 0);
                    });
                    vehicleFullscreenChart.update('none');
                    
                    setTimeout(() => {
                        vehicleFullscreenChart.data = currentData;
                        vehicleFullscreenChart.options.animation = {
                            duration: 1500,
                            easing: 'easeInOutQuart'
                        };
                        vehicleFullscreenChart.update();
                    }, 50);
                });
            }

            // Close on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.classList.contains('show')) {
                    closeVehicleFullscreenChart();
                }
            });
        }

        // Add fullscreen functionality for vehicle chart
        const fullscreenVehicleBtn = document.getElementById('fullscreen-vehicle');
        if (fullscreenVehicleBtn) {
            fullscreenVehicleBtn.addEventListener('click', function() {
                openVehicleFullscreenChart();
            });
        }

        // Setup vehicle fullscreen modal functionality
        setupVehicleFullscreenModal();

// ... existing code ...
    </script>

    <!-- Time Slot Fullscreen Modal -->
    <div id="timeslot-fullscreen-modal" class="fullscreen-modal">
        <button id="timeslot-fullscreen-close" class="fullscreen-close">
            <i class="bx bx-x"></i>
        </button>
        <div class="fullscreen-chart-container">
            <canvas id="timeslot-fullscreen-chart"></canvas>
        </div>
        <div class="fullscreen-controls">
            <button id="timeslot-fullscreen-chart-type">
                <i class="bx bx-bar-chart"></i>
                Change Chart Type
            </button>
            <button id="timeslot-fullscreen-data-labels">
                <i class="bx bx-tag"></i>
                Data Labels
            </button>
            <button id="timeslot-fullscreen-gradient">
                <i class="bx bx-paint-roll"></i>
                Toggle Gradient
            </button>
            <button id="timeslot-fullscreen-animation">
                <i class="bx bx-play"></i>
                Animate
            </button>
            <select id="timeslot-fullscreen-style" class="chart-select">
                <option value="bar">Bar</option>
                <option value="line">Line</option>
                <option value="area">Area</option>
            </select>
        </div>
    </div>

    <script>
// ... existing code ...

        let timeslotFullscreenChart = null;

        function openTimeslotFullscreenChart() {
            const modal = document.getElementById('timeslot-fullscreen-modal');
            const canvas = document.getElementById('timeslot-fullscreen-chart');
            
            if (!modal || !canvas || !timeslotChart) return;
            
            modal.classList.add('show');
            
            // Create a new chart in fullscreen mode with the same data
            if (timeslotFullscreenChart) {
                timeslotFullscreenChart.destroy();
            }
            
            const ctx = canvas.getContext('2d');
            
            // Clone the data and configuration
            const enhancedData = JSON.parse(JSON.stringify(timeslotChart.data));
            
            timeslotFullscreenChart = new Chart(ctx, {
                type: timeslotChart.config.type,
                data: enhancedData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Bookings: ${context.parsed.y}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Bookings',
                                font: {
                                    size: 14
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Time Slots',
                                font: {
                                    size: 14
                                }
                            }
                        }
                    }
                }
            });
        }

        function closeTimeslotFullscreenChart() {
            const modal = document.getElementById('timeslot-fullscreen-modal');
            if (!modal) return;
            
            modal.classList.remove('show');
            
            // Destroy the fullscreen chart to free up resources
            if (timeslotFullscreenChart) {
                setTimeout(() => {
                    timeslotFullscreenChart.destroy();
                    timeslotFullscreenChart = null;
                }, 300);
            }
        }

        function setupTimeslotFullscreenModal() {
            const modal = document.getElementById('timeslot-fullscreen-modal');
            const closeBtn = document.getElementById('timeslot-fullscreen-close');
            const chartTypeBtn = document.getElementById('timeslot-fullscreen-chart-type');
            const dataLabelsBtn = document.getElementById('timeslot-fullscreen-data-labels');
            const gradientBtn = document.getElementById('timeslot-fullscreen-gradient');
            const animationBtn = document.getElementById('timeslot-fullscreen-animation');
            const styleSelect = document.getElementById('timeslot-fullscreen-style');

            if (closeBtn) {
                closeBtn.addEventListener('click', closeTimeslotFullscreenChart);
            }

            if (chartTypeBtn) {
                chartTypeBtn.addEventListener('click', function() {
                    if (!timeslotFullscreenChart) return;
                    const currentType = timeslotFullscreenChart.config.type;
                    const newType = currentType === 'bar' ? 'line' : 'bar';
                    
                    timeslotFullscreenChart.config.type = newType;
                    
                    // Update the main chart type as well
                    timeslotChart.config.type = newType;
                    timeslotChart.update();
                    
                    // Update chart specific options
                    if (newType === 'line') {
                        timeslotFullscreenChart.data.datasets[0].tension = 0.4;
                        timeslotFullscreenChart.data.datasets[0].fill = false;
                    }
                    
                    timeslotFullscreenChart.update();
                });
            }

            if (styleSelect) {
                styleSelect.addEventListener('change', function() {
                    if (!timeslotFullscreenChart) return;
                    const newType = this.value;
                    
                    timeslotFullscreenChart.config.type = newType;
                    
                    if (newType === 'line' || newType === 'area') {
                        timeslotFullscreenChart.data.datasets[0].tension = 0.4;
                        timeslotFullscreenChart.data.datasets[0].fill = newType === 'area' ? 'origin' : false;
                    }
                    
                    // Update the main chart
                    timeslotChart.config.type = newType;
                    if (newType === 'line' || newType === 'area') {
                        timeslotChart.data.datasets[0].tension = 0.4;
                        timeslotChart.data.datasets[0].fill = newType === 'area' ? 'origin' : false;
                    }
                    timeslotChart.update();
                    
                    timeslotFullscreenChart.update();
                });
            }

            if (dataLabelsBtn) {
                dataLabelsBtn.addEventListener('click', function() {
                    if (!timeslotFullscreenChart) return;
                    const hasLabels = timeslotFullscreenChart.options.plugins.datalabels?.display;
                    
                    timeslotFullscreenChart.options.plugins.datalabels = {
                        display: !hasLabels,
                        color: 'white',
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        borderRadius: 4,
                        padding: 4,
                        font: {
                            weight: 'bold'
                        },
                        formatter: (value) => value
                    };
                    
                    timeslotFullscreenChart.update();
                });
            }

            if (gradientBtn) {
                gradientBtn.addEventListener('click', function() {
                    if (!timeslotFullscreenChart) return;
                    const ctx = timeslotFullscreenChart.ctx;
                    const dataset = timeslotFullscreenChart.data.datasets[0];
                    const currentColor = dataset.backgroundColor;
                    const hasGradient = typeof currentColor === 'object' && !Array.isArray(currentColor);
                    
                    if (!hasGradient) {
                        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.8)');
                        gradient.addColorStop(1, 'rgba(99, 102, 241, 0.1)');
                        dataset.backgroundColor = gradient;
                    } else {
                        dataset.backgroundColor = 'rgba(99, 102, 241, 0.5)';
                    }
                    
                    timeslotFullscreenChart.update();
                });
            }

            if (animationBtn) {
                animationBtn.addEventListener('click', function() {
                    if (!timeslotFullscreenChart) return;
                    
                    const currentData = JSON.parse(JSON.stringify(timeslotFullscreenChart.data));
                    
                    timeslotFullscreenChart.data.datasets.forEach(dataset => {
                        dataset.data = dataset.data.map(() => 0);
                    });
                    timeslotFullscreenChart.update('none');
                    
                    setTimeout(() => {
                        timeslotFullscreenChart.data = currentData;
                        timeslotFullscreenChart.options.animation = {
                            duration: 1500,
                            easing: 'easeInOutQuart'
                        };
                        timeslotFullscreenChart.update();
                    }, 50);
                });
            }

            // Close on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.classList.contains('show')) {
                    closeTimeslotFullscreenChart();
                }
            });
        }

        // Add fullscreen functionality for timeslot chart
        const fullscreenTimeslotBtn = document.getElementById('fullscreen-timeslot');
        if (fullscreenTimeslotBtn) {
            fullscreenTimeslotBtn.addEventListener('click', function() {
                openTimeslotFullscreenChart();
            });
        }

        // Setup timeslot fullscreen modal functionality
        setupTimeslotFullscreenModal();

// ... existing code ...
    </script>

    <!-- Trainer Performance Fullscreen Modal -->
    <div id="trainer-fullscreen-modal" class="fullscreen-modal">
        <button id="trainer-fullscreen-close" class="fullscreen-close">
            <i class="bx bx-x"></i>
        </button>
        <div class="fullscreen-chart-container">
            <canvas id="trainer-fullscreen-chart"></canvas>
        </div>
        <div class="fullscreen-controls">
            <!-- <select id="trainer-fullscreen-metric" class="chart-select">
                <option value="revenue">Revenue Generated</option>
                <option value="students">Total Students</option>
                <option value="hours">Training Hours</option>
                <option value="rating">Average Rating</option>
            </select> -->
            <button id="trainer-fullscreen-chart-type">
                <i class="bx bx-bar-chart"></i>
                Change Chart Type
            </button>
            <!-- <button id="trainer-fullscreen-sort">
                <i class="bx bx-sort"></i>
                Sort Data
            </button> -->
            <button id="trainer-fullscreen-data-labels">
                <i class="bx bx-tag"></i>
                Data Labels
            </button>
            <button id="trainer-fullscreen-gradient">
                <i class="bx bx-paint-roll"></i>
                Toggle Gradient
            </button>
            <button id="trainer-fullscreen-animation">
                <i class="bx bx-play"></i>
                Animate
            </button>
        </div>
    </div>

    <script>
// ... existing code ...

        let trainerChart = null;
        let trainerFullscreenChart = null;

        function initializeTrainerChart() {
            const ctx = document.getElementById('trainerChart').getContext('2d');
            
            trainerChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Revenue Generated',
                        data: [],
                        backgroundColor: 'rgba(99, 102, 241, 0.5)',
                        borderColor: 'rgb(99, 102, 241)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Revenue (₹)'
                            }
                        }
                    }
                }
            });
        }

        function updateTrainerChart(metric = 'revenue') {
            const year = document.getElementById('select-year').value;
            const month = document.getElementById('select-month').value;
            
            fetch(`api/trainer-performance.php?year=${year}&month=${month}&metric=${metric}`)
                .then(response => response.json())
                .then(data => {
                    trainerChart.data.labels = data.labels;
                    trainerChart.data.datasets[0].data = data.values;
                    trainerChart.data.datasets[0].label = data.metricLabel;
                    
                    trainerChart.options.scales.y.title.text = data.yAxisLabel;
                    
                    // Update colors based on metric
                    const colors = getMetricColors(metric);
                    trainerChart.data.datasets[0].backgroundColor = colors.background;
                    trainerChart.data.datasets[0].borderColor = colors.border;
                    
                    trainerChart.update();
                })
                .catch(error => {
                    console.error('Error fetching trainer performance data:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load trainer performance data.'
                    });
                });
        }

        function getMetricColors(metric) {
            switch(metric) {
                case 'revenue':
                    return {
                        background: 'rgba(99, 102, 241, 0.5)',
                        border: 'rgb(99, 102, 241)'
                    };
                case 'students':
                    return {
                        background: 'rgba(16, 185, 129, 0.5)',
                        border: 'rgb(16, 185, 129)'
                    };
                case 'hours':
                    return {
                        background: 'rgba(245, 158, 11, 0.5)',
                        border: 'rgb(245, 158, 11)'
                    };
                case 'rating':
                    return {
                        background: 'rgba(236, 72, 153, 0.5)',
                        border: 'rgb(236, 72, 153)'
                    };
                default:
                    return {
                        background: 'rgba(99, 102, 241, 0.5)',
                        border: 'rgb(99, 102, 241)'
                    };
            }
        }

        function openTrainerFullscreenChart() {
            const modal = document.getElementById('trainer-fullscreen-modal');
            const canvas = document.getElementById('trainer-fullscreen-chart');
            
            if (!modal || !canvas || !trainerChart) return;
            
            modal.classList.add('show');
            
            if (trainerFullscreenChart) {
                trainerFullscreenChart.destroy();
            }
            
            const ctx = canvas.getContext('2d');
            const enhancedData = JSON.parse(JSON.stringify(trainerChart.data));
            
            trainerFullscreenChart = new Chart(ctx, {
                type: trainerChart.config.type,
                data: enhancedData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const metric = document.getElementById('trainer-metric').value;
                                    const value = context.parsed.y;
                                    switch(metric) {
                                        case 'revenue':
                                            return `Revenue: ₹${value.toLocaleString()}`;
                                        case 'students':
                                            return `Students: ${value}`;
                                        case 'hours':
                                            return `Hours: ${value}`;
                                        case 'rating':
                                            return `Rating: ${value.toFixed(1)}`;
                                        default:
                                            return `${context.dataset.label}: ${value}`;
                                    }
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: trainerChart.options.scales.y.title.text,
                                font: {
                                    size: 14
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Trainers',
                                font: {
                                    size: 14
                                }
                            }
                        }
                    }
                }
            });
        }

        function closeTrainerFullscreenChart() {
            const modal = document.getElementById('trainer-fullscreen-modal');
            if (!modal) return;
            
            modal.classList.remove('show');
            
            if (trainerFullscreenChart) {
                setTimeout(() => {
                    trainerFullscreenChart.destroy();
                    trainerFullscreenChart = null;
                }, 300);
            }
        }

        function setupTrainerFullscreenModal() {
            const modal = document.getElementById('trainer-fullscreen-modal');
            const closeBtn = document.getElementById('trainer-fullscreen-close');
            const chartTypeBtn = document.getElementById('trainer-fullscreen-chart-type');
            const metricSelect = document.getElementById('trainer-fullscreen-metric');
            const sortBtn = document.getElementById('trainer-fullscreen-sort');
            const dataLabelsBtn = document.getElementById('trainer-fullscreen-data-labels');
            const gradientBtn = document.getElementById('trainer-fullscreen-gradient');
            const animationBtn = document.getElementById('trainer-fullscreen-animation');

            if (closeBtn) {
                closeBtn.addEventListener('click', closeTrainerFullscreenChart);
            }

            if (chartTypeBtn) {
                chartTypeBtn.addEventListener('click', function() {
                    if (!trainerFullscreenChart) return;
                    const currentType = trainerFullscreenChart.config.type;
                    const newType = currentType === 'bar' ? 'line' : 'bar';
                    
                    trainerFullscreenChart.config.type = newType;
                    trainerChart.config.type = newType;
                    
                    if (newType === 'line') {
                        trainerFullscreenChart.data.datasets[0].tension = 0.4;
                        trainerChart.data.datasets[0].tension = 0.4;
                    }
                    
                    trainerFullscreenChart.update();
                    trainerChart.update();
                });
            }

            if (metricSelect) {
                metricSelect.addEventListener('change', function() {
                    const metric = this.value;
                    document.getElementById('trainer-metric').value = metric;
                    updateTrainerChart(metric);
                });
            }

            if (sortBtn) {
                sortBtn.addEventListener('click', function() {
                    if (!trainerFullscreenChart) return;
                    
                    const data = [...trainerFullscreenChart.data.datasets[0].data];
                    const labels = [...trainerFullscreenChart.data.labels];
                    const colors = [...trainerFullscreenChart.data.datasets[0].backgroundColor];
                    
                    const sorted = data.map((value, index) => ({
                        value,
                        label: labels[index],
                        color: colors[index]
                    })).sort((a, b) => b.value - a.value);
                    
                    trainerFullscreenChart.data.datasets[0].data = sorted.map(item => item.value);
                    trainerFullscreenChart.data.labels = sorted.map(item => item.label);
                    trainerFullscreenChart.data.datasets[0].backgroundColor = sorted.map(item => item.color);
                    
                    trainerFullscreenChart.update();
                });
            }

            if (dataLabelsBtn) {
                dataLabelsBtn.addEventListener('click', function() {
                    if (!trainerFullscreenChart) return;
                    const hasLabels = trainerFullscreenChart.options.plugins.datalabels?.display;
                    
                    trainerFullscreenChart.options.plugins.datalabels = {
                        display: !hasLabels,
                        color: 'white',
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        borderRadius: 4,
                        padding: 4,
                        font: {
                            weight: 'bold'
                        },
                        formatter: (value) => {
                            const metric = document.getElementById('trainer-metric').value;
                            switch(metric) {
                                case 'revenue':
                                    return '₹' + value.toLocaleString();
                                case 'rating':
                                    return value.toFixed(1);
                                default:
                                    return value;
                            }
                        }
                    };
                    
                    trainerFullscreenChart.update();
                });
            }

            if (gradientBtn) {
                gradientBtn.addEventListener('click', function() {
                    if (!trainerFullscreenChart) return;
                    const ctx = trainerFullscreenChart.ctx;
                    const dataset = trainerFullscreenChart.data.datasets[0];
                    const currentColor = dataset.backgroundColor;
                    const metric = document.getElementById('trainer-metric').value;
                    const colors = getMetricColors(metric);
                    
                    const hasGradient = typeof currentColor === 'object' && !Array.isArray(currentColor);
                    
                    if (!hasGradient) {
                        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                        gradient.addColorStop(0, colors.border);
                        gradient.addColorStop(1, 'rgba(255, 255, 255, 0.1)');
                        dataset.backgroundColor = gradient;
                    } else {
                        dataset.backgroundColor = colors.background;
                    }
                    
                    trainerFullscreenChart.update();
                });
            }

            if (animationBtn) {
                animationBtn.addEventListener('click', function() {
                    if (!trainerFullscreenChart) return;
                    
                    const currentData = JSON.parse(JSON.stringify(trainerFullscreenChart.data));
                    
                    trainerFullscreenChart.data.datasets.forEach(dataset => {
                        dataset.data = dataset.data.map(() => 0);
                    });
                    trainerFullscreenChart.update('none');
                    
                    setTimeout(() => {
                        trainerFullscreenChart.data = currentData;
                        trainerFullscreenChart.options.animation = {
                            duration: 1500,
                            easing: 'easeInOutQuart'
                        };
                        trainerFullscreenChart.update();
                    }, 50);
                });
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.classList.contains('show')) {
                    closeTrainerFullscreenChart();
                }
            });
        }

        // Initialize trainer chart
        initializeTrainerChart();
        // Initial update of trainer chart
        updateTrainerChart();

        // Add event listeners for trainer chart controls
        const trainerMetricSelect = document.getElementById('trainer-metric');
        if (trainerMetricSelect) {
            trainerMetricSelect.addEventListener('change', function() {
                updateTrainerChart(this.value);
            });
        }

        // Add event listeners for year and month changes
        const yearSelect = document.getElementById('select-year');
        const monthSelect = document.getElementById('select-month');

        if (yearSelect) {
            yearSelect.addEventListener('change', function() {
                const currentMetric = document.getElementById('trainer-metric').value;
                updateTrainerChart(currentMetric);
            });
        }

        if (monthSelect) {
            monthSelect.addEventListener('change', function() {
                const currentMetric = document.getElementById('trainer-metric').value;
                updateTrainerChart(currentMetric);
            });
        }

        const trainerChartTypeBtn = document.getElementById('trainer-chart-type');
        if (trainerChartTypeBtn) {
            trainerChartTypeBtn.addEventListener('click', function() {
                if (!trainerChart) return;
                const currentType = trainerChart.config.type;
                const newType = currentType === 'bar' ? 'line' : 'bar';
                
                trainerChart.config.type = newType;
                if (newType === 'line') {
                    trainerChart.data.datasets[0].tension = 0.4;
                }
                trainerChart.update();
            });
        }

        // Add fullscreen functionality for trainer chart
        const fullscreenTrainerBtn = document.getElementById('fullscreen-trainer');
        if (fullscreenTrainerBtn) {
            fullscreenTrainerBtn.addEventListener('click', function() {
                openTrainerFullscreenChart();
            });
        }

        // Setup trainer fullscreen modal functionality
        setupTrainerFullscreenModal();

// ... existing code ...
    </script>

</body>

</html>