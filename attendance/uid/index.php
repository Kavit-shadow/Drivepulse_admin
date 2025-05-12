<!DOCTYPE html>
<html lang="en">

<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Drive Pulse Driving School offers a comprehensive attendance system for tracking customer attendance efficiently.">
        <meta name="keywords" content="driving school, motor training, attendance tracking, customer attendance, Ahmedabad, Gujarat, driving lessons, driving courses">
        <meta name="author" content="Eternal Bytes">
        <meta property="og:title" content="Attendance System - Drive Pulse Driving School">
        <meta property="og:description" content="Track customer attendance with our user-friendly attendance system at Drive Pulse Driving School.">
        <meta property="og:image" content="http://demo-drivepulse.eternalbytes.in/assets/name.png">
        <meta property="og:url" content="https://maps.app.goo.gl/FYHLtcAZLfDRFLr66">
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="Drive Pulse Driving School">
        <link rel="shortcut icon" type="image/png" href="../../assets/logo.png" />
        <link rel="apple-touch-icon" href="http://demo-drivepulse.eternalbytes.in/assets/name.png">
        <title>Attendance System | Drive Pulse Driving School - Efficient Attendance Tracking</title>
    <style>
        /* Modal styling */
        dialog {
            padding: 0;
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.15);
            max-width: 95%;
            width: 1500px;
            margin: 20px auto;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            font-family: 'Poppins', sans-serif;
            max-height: 90vh;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            /* Smooth scrolling on iOS */
        }

        dialog::backdrop {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(3px);
        }

        .modal-content {
            position: relative;
            padding: 24px 16px;
            background: #fff;
            border-radius: 12px;
        }

        .close {
            position: absolute;
            top: 12px;
            right: 12px;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
            transition: all 0.2s;
            padding: 8px;
            border-radius: 50%;
            z-index: 10;
        }

        .close:hover {
            color: #333;
            background: #f1f5f9;
        }

        .modal-table-container {
            display: flex;
            gap: 24px;
            margin-top: 20px;
            flex-direction: column;
        }

        .employee-images {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        .employee-images h4 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 1rem;
            font-weight: 600;
            text-align: center;
        }

        .employee-images img:not(#empuid-logo) {
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
            margin-bottom: 20px;
            max-height: 300px;
            object-fit: contain;
        }

        .employee-images button {
            width: 100%;
            padding: 12px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
        }

        .employee-info {
            background: #f8fafc;
            padding: 20px 16px;
            border-radius: 8px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .info-item strong {
            color: #334155;
            margin-right: 8px;
            font-size: 0.9rem;
            display: block;
            margin-bottom: 4px;
        }

        .info-item span {
            color: #64748b;
            font-size: 0.95rem;
            word-break: break-word;
        }

        /* Tablet breakpoint */
        @media (min-width: 768px) {
            .modal-content {
                padding: 28px;
            }

            .employee-info {
                grid-template-columns: repeat(2, 1fr);
                padding: 24px;
            }
        }

        /* Desktop breakpoint */
        @media (min-width: 1024px) {
            .modal-table-container {
                flex-direction: row;
            }

            .employee-images {
                flex: 0 0 300px;
            }

            .employee-info {
                flex: 1;
            }
        }

        /* Software Access Status Styles */
        .access-granted,
        .access-denied,
        .access-error {
            padding: 12px;
            border-radius: 6px;
            margin: 8px 0;
            font-size: 0.9rem;
        }

        .access-granted {
            background-color: #e8f5e9;
            border: 1px solid #4caf50;
            color: #2e7d32;
        }

        .access-denied {
            background-color: #ffebee;
            border: 1px solid #ef5350;
            color: #c62828;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .access-error {
            background-color: #fff3e0;
            border: 1px solid #ff9800;
            color: #e65100;
        }

        .access-granted i,
        .access-denied i,
        .access-error i {
            margin-right: 8px;
            font-size: 1.1em;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
        }

        .access-details {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid rgba(76, 175, 80, 0.3);
            font-size: 0.85rem;
        }

        .create-access-btn {
            padding: 8px 12px;
            border-radius: 4px;
            border: none;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            transition: background-color 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .error-message {
            margin-top: 6px;
            font-size: 0.85rem;
        }

        .access-status {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {

            .restore-btn,
            .view-btn {
                padding: 6px 10px !important;
                font-size: 12px !important;
                display: block !important;
                width: 100% !important;
                margin: 5px 0 !important;
            }

            .restore-btn i,
            .view-btn i {
                font-size: 14px !important;
            }

            td[data-cell='Action'] {
                display: flex !important;
                flex-direction: column !important;
                gap: 5px !important;
                padding: 10px 5px !important;
            }

            table td {
                padding: 10px 5px !important;
                font-size: 13px !important;
            }

            table th {
                padding: 10px 5px !important;
                font-size: 14px !important;
            }

            .table-data .order table {
                min-width: 500px !important;
            }

            .table-data {
                overflow-x: auto !important;
            }
        }

        @media (max-width: 480px) {
            .table-data .order table {
                min-width: 400px !important;
            }

            table td {
                font-size: 12px !important;
            }

            table th {
                font-size: 13px !important;
            }
        }
    </style>


    <style>
        :root {
            /* Cool Ocean */
            --gradient-ocean: linear-gradient(-45deg, #00c6fb, #005bea, #0575E6, #38ef7d);

            /* Sunset Vibes */
            --gradient-sunset: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);

            /* Royal Purple */
            --gradient-royal: linear-gradient(-45deg, #4a00e0, #8e2de2, #6a3093, #a044ff);

            /* Forest Fresh */
            --gradient-forest: linear-gradient(-45deg, #134E5E, #71B280, #2C5364, #42A367);

            /* Fire & Ice */
            --gradient-fire-ice: linear-gradient(-45deg, #ff0f7b, #f89b29, #0575E6, #00f2fe);

            /* Desert Sand */
            --gradient-desert: linear-gradient(-45deg, #FFB75E, #ED8F03, #c79081, #dfa579);

            /* Northern Lights */
            --gradient-aurora: linear-gradient(-45deg, #1eecff, #72e67c, #00ff87, #72faca);

            /* Cherry Blossom */
            --gradient-cherry: linear-gradient(-45deg, #ff758c, #ff7eb3, #ef8172, #fca5a5);

            /* Deep Space */
            --gradient-space: linear-gradient(-45deg, #000428, #004e92, #2c3e50, #3498db);

            /* Tropical Paradise */
            --gradient-tropical: linear-gradient(-45deg, #00b09b, #96c93d, #02aab0, #00cdac);

            /* Golden Hour */
            --gradient-golden: linear-gradient(-45deg, #f7971e, #ffd200, #f9d423, #e65c00);

            /* Mystic Forest */
            --gradient-mystic: linear-gradient(-45deg, #43c6ac, #191654, #20bf55, #01baef);

            /* Cotton Candy */
            --gradient-candy: linear-gradient(-45deg, #ffafbd, #ffc3a0, #ff9a9e, #fad0c4);

            /* Electric Night */
            --gradient-electric: linear-gradient(-45deg, #0072ff, #00c6ff, #240b36, #c31432);

            /* Mountain Peaks */
            --gradient-mountain: linear-gradient(-45deg, #5d4157, #a8caba, #2c3e50, #bdc3c7);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            /* background: var(--gradient-sunset);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite; */
            background-color: #f8f9fa;
        }

        @keyframes gradientBG {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        header {
            background: var(--gradient-royal);
            background-size: 300% 300%;
            animation: headerGradient 8s ease infinite;
            color: #fff;
            text-align: center;
            padding: 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        @keyframes headerGradient {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        header h1 {
            font-size: 2.5rem;
            font-weight: 600;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .logo {
            max-width: 200px;
            width: 100%;
        }

        main {
            flex: 1;
            max-width: 600px;
            margin: 2rem auto;
            padding: 0 1rem;
            width: 100%;
        }

        .pin-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.5s ease;
            backdrop-filter: blur(10px);
            width: 100%;
        }

        .pin-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }

        .pin-container h2 {
            color: #4a00e0;
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
        }

        .pin-input {
            display: flex;
            gap: 0.8rem;
            justify-content: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .pin-input input {
            width: 45px;
            height: 55px;
            text-align: center;
            font-size: 1.8rem;
            border: 2px solid #8e2de2;
            border-radius: 8px;
            background: #f8f9fa;
            transition: all 0.5s ease;
        }

        .pin-input input:focus {
            border-color: #4a00e0;
            box-shadow: 0 0 20px rgba(142, 45, 226, 0.3);
            background: #fff;
        }

        .note-input {
            width: 100%;
            margin: 1.5rem 0;
        }

        .note-input label {
            color: #4a00e0;
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: block;
        }

        .note-input textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid #8e2de2;
            border-radius: 8px;
            resize: vertical;
            min-height: 120px;
            font-family: inherit;
            transition: all 0.5s ease;
        }

        .note-input textarea:focus {
            border-color: #4a00e0;
            box-shadow: 0 0 20px rgba(142, 45, 226, 0.3);
        }

        .remember-pin {
            margin: 1.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .remember-pin input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .remember-pin label {
            color: #4a00e0;
            cursor: pointer;
        }

        button[type="submit"],
        #viewAttendanceButton {
            width: 100%;
            padding: 1rem;
            background: var(--gradient-royal);
            background-size: 300% 300%;
            animation: buttonGradient 8s ease infinite;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.5s ease;
        }

        #resetRemember {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            margin-top: 0.5rem;
            width: auto;
            display: inline-block;
        }

        #resetRemember:hover {
            background: #c82333;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
        }

        @keyframes buttonGradient {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        button[type="submit"]:hover,
        #viewAttendanceButton:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(142, 45, 226, 0.3);
        }

        footer {
            background: var(--gradient-royal);
            background-size: 300% 300%;
            animation: footerGradient 8s ease infinite;
            color: #fff;
            text-align: center;
            padding: 1rem;
            margin-top: auto;
            width: 100%;
        }

        @keyframes footerGradient {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        footer p {
            margin: 0.5rem 0;
            font-size: 1rem;
        }

        footer a {
            color: #fff;
            text-decoration: none;
            word-break: break-all;
        }

        footer a:hover {
            text-decoration: underline;
        }

        .social-media {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 0.5rem;
            flex-wrap: wrap;
        }

        .social-media img {
            width: 24px;
            height: 24px;
            transition: transform 0.2s;
        }

        .social-media img:hover {
            transform: scale(1.1);
        }

        @media (max-width: 480px) {
            .pin-input input {
                width: 35px;
                height: 45px;
                font-size: 1.2rem;
            }

            .pin-container {
                padding: 1.2rem;
            }

            .pin-container h2 {
                font-size: 1.5rem;
            }

            .logo {
                max-width: 150px;
            }

            main {
                margin: 1rem auto;
            }

            .note-input textarea {
                min-height: 100px;
            }

            button[type="submit"] {
                font-size: 1rem;
                padding: 0.8rem;
            }

            .remember-pin input[type="checkbox"] {
                width: 16px;
                height: 16px;
            }

            .remember-pin label {
                font-size: 0.9rem;
            }

            footer {
                padding: 0.8rem 0.5rem;
            }

            footer p {
                font-size: 0.85rem;
                padding: 0 0.5rem;
            }

            .social-media {
                gap: 0.8rem;
                padding: 0.5rem;
            }

            .social-media img {
                width: 20px;
                height: 20px;
            }
        }

        @media (max-width: 320px) {
            .pin-input input {
                width: 30px;
                height: 40px;
                font-size: 1rem;
            }

            .pin-container {
                padding: 1rem;
            }

            .logo {
                max-width: 120px;
            }

            footer {
                padding: 0.6rem 0.3rem;
            }

            footer p {
                font-size: 0.8rem;
                padding: 0 0.3rem;
            }

            .social-media {
                gap: 0.6rem;
            }

            .social-media img {
                width: 18px;
                height: 18px;
            }
        }
    </style>
</head>

<body>
    <header>
        <img src="http://demo-drivepulse.eternalbytes.in/assets/name.png" alt="Company Logo" class="logo">
        <!-- <h1>Attendance System</h1> -->
    </header>

    <main>
        <div style="text-align: center; margin-bottom: 2rem;">
            <button type="button" class="attendance-btn" id="viewAttendanceButton">View Attendance Records</button>
        </div>

        <dialog id="attendanceModal">
            <div class="modal-content" style="padding: 20px;">
                <button class="close" style="font-size: 28px; font-weight: bold; cursor: pointer; color: #aaa; border: none; background: none;">&times;</button>
                <h2 style="margin-top: 0;">Attendance Details</h2>

                <div class="modal-table-container">
                    <div id="attendanceContent">
                        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                            <thead>
                                <tr>
                                    <th style="padding: 12px; border: 1px solid #ddd; background: #f4f4f4;">Sr No.</th>
                                    <th style="padding: 12px; border: 1px solid #ddd; background: #f4f4f4;">Customer UID</th>
                                    <th style="padding: 12px; border: 1px solid #ddd; background: #f4f4f4;">Customer Name</th>
                                    <th style="padding: 12px; border: 1px solid #ddd; background: #f4f4f4;">Date</th>
                                    <th style="padding: 12px; border: 1px solid #ddd; background: #f4f4f4;">Time In</th>
                                    <th style="padding: 12px; border: 1px solid #ddd; background: #f4f4f4;">Time Out</th>
                                    <th style="padding: 12px; border: 1px solid #ddd; background: #f4f4f4;">Attendance Time</th>
                                    <th style="padding: 12px; border: 1px solid #ddd; background: #f4f4f4;">Vehicle Name</th>
                                    <th style="padding: 12px; border: 1px solid #ddd; background: #f4f4f4;">Trainer Name</th>
                                    <th style="padding: 12px; border: 1px solid #ddd; background: #f4f4f4;">Note</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </dialog>

        <div class="pin-container">
            <h2>Enter UID</h2>
            <form id="pinForm">
                <div class="pin-input">
                    <input type="password" maxlength="1" pattern="[A-Za-z0-9]" required>
                    <input type="password" maxlength="1" pattern="[A-Za-z0-9]" required>
                    <input type="password" maxlength="1" pattern="[A-Za-z0-9]" required>
                    <input type="password" maxlength="1" pattern="[A-Za-z0-9]" required>
                    <input type="password" maxlength="1" pattern="[A-Za-z0-9]" required>
                    <input type="password" maxlength="1" pattern="[A-Za-z0-9]" required>
                </div>

                <div class="note-input">
                    <label for="note">Note:</label>
                    <textarea id="note" placeholder="Enter any additional notes here..."></textarea>
                </div>

                <div class="remember-pin">
                    <input type="checkbox" id="remember">
                    <label for="remember">Remember UID to view attendance history</label>
                    <button type="button" id="resetRemember" style="margin-left: 10px; padding: 5px 10px; font-size: 12px;">Reset Remember</button>
                </div>

                <button type="submit">Submit</button>
            </form>
        </div>
    </main>

   <footer style="text-align: center; padding: 20px;  color: #ffffff;">
        <p style="margin: 0;">&copy; 2024 DrivePulse Driving School. All rights reserved.</p>
        <div class="footer" style="margin-top: 10px;">
            <p style="margin: 0;">Contact us: <a href="mailto:DrivePulse@gmail.com" style="color: #007bff; text-decoration: none;">DrivePulse@gmail.com</a> | +91 6353149071</p>
            <div class="social-media" style="margin-top: 10px;">
                <a href="#" target="_blank" style="margin-right: 10px;">
                    <img src="https://patelmotordrivingschool.com/storage/images/icons/icon-google-maps.png" alt="Google Maps" style="width: 24px; height: 24px;">
                </a>
                <a href="#" target="_blank" style="margin-right: 10px;">
                    <img src="https://patelmotordrivingschool.com/storage/images/icons/icon-facebook.png" alt="Facebook" style="width: 24px; height: 24px;">
                </a>
                <a href="#" target="_blank">
                    <img src="https://patelmotordrivingschool.com/storage/images/icons/icon-instagram.png" alt="Instagram" style="width: 24px; height: 24px;">
                </a>
            </div>
        </div>
        <style>
            .badge {
                background-color: #5700a8; /* Match the theme color */
                color: #ffffff;
                padding: 10px 15px;
                border-radius: 20px;
                display: inline-flex;
                align-items: center;
                gap: 20px;
                font-weight: bold;
                transition: background-color 0.3s ease, transform 0.3s ease; /* Added ease for smoother transitions */
                max-width: 100%; /* Ensure it doesn't overflow on smaller screens */
                box-sizing: border-box; /* Include padding in width calculation */
            }
            .badge:hover {
                background-color: #3a0070; /* Darker shade for hover effect */
                transform: scale(1.05); /* Added scale effect on hover */
            }
            .badge img {
                width: 20px;
                height: 20px;
                transition: transform 0.3s ease; /* Added ease for smoother transitions */
            }
            .badge img:hover {
                transform: scale(1.1);
            }
            .badge a {
                color: #ffffff;
                text-decoration: none;
                font-weight: bold;
            }
            .dev-by {
                color: #ffffff;
                text-decoration: none;
                font-weight: bold;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 5px;
            }   

            /* Responsive adjustments */
            @media (max-width: 600px) {
                .badge {
                    flex-direction: column; /* Stack items on smaller screens */
                    align-items: center; /* Center items */
                    padding: 8px 10px; /* Adjust padding for smaller screens */
                }
                .badge img {
                    width: 16px; /* Smaller logo on mobile */
                    height: 16px;
                }
                .dev-by {
                    font-size: 12px;
                }
            }
        </style>
        
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const emp_uid = urlParams.get('id') || null;
            const inputs = document.querySelectorAll('.pin-input input');
            const form = document.getElementById('pinForm');

            // Helper functions for cookies
            function setCookie(name, value, days) {
                let expires = "";
                if (days) {
                    const date = new Date();
                    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                    expires = "; expires=" + date.toUTCString();
                }
                document.cookie = name + "=" + (value || "") + expires + "; path=/";
            }

            function getCookie(name) {
                const nameEQ = name + "=";
                const ca = document.cookie.split(';');
                for (let i = 0; i < ca.length; i++) {
                    let c = ca[i];
                    while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
                }
                return null;
            }

            function eraseCookie(name) {
                document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            }

            // Handle reset remember button click
            document.getElementById('resetRemember').addEventListener('click', function() {
                eraseCookie('rememberedPin');
                Swal.fire({
                    icon: 'success',
                    title: 'Reset Successful',
                    text: 'Remembered UID has been cleared',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            });

            // Check if employee UID exists
            // console.log(emp_uid);
            if (emp_uid) {
                $.ajax({
                    url: '../checkEmpUID.php',
                    method: 'POST',
                    data: {
                        uid: emp_uid
                    },
                    success: function(response) {
                        if (!response.success || !response.exists) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid QR Code',
                                text: 'This QR code is not valid for any employee',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = '../contact';
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to verify QR code',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = '../contact';
                        });
                    }
                });
            }

            // Handle input navigation
            inputs.forEach((input, index) => {
                input.addEventListener('input', function() {
                    if (this.value && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                });

                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && !this.value && index > 0) {
                        inputs[index - 1].focus();
                    }
                });
            });

            // Load remembered PIN if exists
            const savedPin = getCookie('rememberedPin');
            if (savedPin) {
                const pinArray = savedPin.split('');
                inputs.forEach((input, index) => {
                    input.value = pinArray[index] || '';
                });
                document.getElementById('remember').checked = true;
            }

            // Handle form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const pin = Array.from(inputs).map(input => input.value).join('').toUpperCase();
                const note = document.getElementById('note').value;
                const remember = document.getElementById('remember').checked;

                if (!pin) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please enter your PIN'
                    });
                    return;
                }

                // Verify customer UID
                $.ajax({
                    url: '../checkUID.php',
                    method: 'POST',
                    data: {
                        uid: pin
                    },
                    success: function(response) {
                        if (response.success && response.exists) {
                            // Handle remember PIN
                            if (remember) {
                                setCookie('rememberedPin', pin, 15); // Store for 15 days
                            } else {
                                eraseCookie('rememberedPin');
                            }

                            // Add attendance record
                            $.ajax({
                                url: '../addCustAttendance.php',
                                method: 'POST',
                                data: {
                                    uid: pin.toUpperCase(),
                                    note: note,
                                    emp_uid: emp_uid.toUpperCase()
                                },
                                success: function(attendanceResponse) {
                                    try {
                                        attendanceResponse = JSON.parse(attendanceResponse);
                                        if (attendanceResponse.success) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Success!',
                                                text: 'Attendance recorded successfully',
                                                timer: 2000,
                                                showConfirmButton: false
                                            });
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error',
                                                text: attendanceResponse.message || 'Failed to record attendance. Please try again.'
                                            });
                                        }
                                    } catch (e) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: 'Invalid server response. Please try again.'
                                        });
                                    }
                                },
                                error: function(xhr, status, error) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Failed to record attendance. Please try again.'
                                    });
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid PIN',
                                text: 'Please enter a valid PIN'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to verify PIN. Please try again.'
                        });
                    }
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const attendanceBtn = document.querySelector('.attendance-btn');
            const attendanceModal = document.getElementById('attendanceModal');
            const closeBtn = document.querySelector('#attendanceModal .close');

            if (attendanceBtn) {
                attendanceBtn.addEventListener('click', function() {
                    attendanceModal.showModal();
                    loadAttendanceData();
                });
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    attendanceModal.close();
                });
            }

            if (attendanceModal) {
                attendanceModal.addEventListener('click', function(event) {
                    if (event.target === this) {
                        this.close();
                    }
                });
            }



            // Function to load attendance data
            function loadAttendanceData() {
                function getCookie(name) {
                    const nameEQ = name + "=";
                    const ca = document.cookie.split(';');
                    for (let i = 0; i < ca.length; i++) {
                        let c = ca[i];
                        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
                    }
                    return null;
                }
                const urlParams = new URLSearchParams(window.location.search);
                const id = getCookie('rememberedPin');
                // console.log(id);

                if (!id) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No ID found. Please enter your UID first.',
                        confirmButtonColor: '#3085d6'
                    }).then(() => {
                        attendanceModal.close();
                    });
                    return;
                }

                $.ajax({
                    url: '../../api_ajax/get_attendance.php',
                    method: 'POST',
                    data: {
                        id: id
                    },
                    success: function(response) {
                        try {
                            // Handle empty or invalid response
                            if (!response) {
                                throw new Error('Empty response received');
                            }

                            const data = typeof response === 'string' ? JSON.parse(response) : response;

                            if (!Array.isArray(data)) {
                                throw new Error('Invalid data format');
                            }

                            // Handle error response from server
                            if (data.error) {
                                throw new Error(data.error);
                            }

                            // Sort data by date in ascending order (oldest first)
                            data.sort((a, b) => {
                                const dateA = new Date(a.date);
                                const dateB = new Date(b.date);
                                return dateA - dateB;
                            });

                            const tbody = document.getElementById('attendanceTableBody');
                            if (!tbody) {
                                throw new Error('Table body element not found');
                            }

                            tbody.innerHTML = '';

                            // Get all dates between first and last attendance
                            const allDates = [];
                            if (data.length > 0) {
                                const startDate = new Date(data[0].date);
                                const endDate = new Date(data[data.length - 1].date);

                                for (let d = new Date(startDate); d <= endDate; d.setDate(d.getDate() + 1)) {
                                    allDates.push(new Date(d));
                                }
                            }
                            
                            // Add summary row
                            const summaryRow = document.createElement('tr');
                            const presentCount = data.length;
                            const absentCount = allDates.length - presentCount;
                            const attendancePercentage = allDates.length > 0 
                                ? ((presentCount / allDates.length) * 100).toFixed(1) 
                                : 0;

                            summaryRow.innerHTML = `
                                <td colspan="12" style="padding: 16px; border: 1px solid #ddd; background-color: #f8f9fa;">
                                    <div style="display: flex; justify-content: space-around; align-items: center;">
                                        <div style="text-align: center;">
                                            <div style="font-weight: bold; font-size: 1.1em; color: #28a745;">Present</div>
                                            <div style="font-size: 1.2em;">${presentCount} days</div>
                                        </div>
                                        <div style="text-align: center;">
                                            <div style="font-weight: bold; font-size: 1.1em; color: #dc3545;">Absent</div>
                                            <div style="font-size: 1.2em;">${absentCount} days</div>
                                        </div>
                                    </div>
                                </td>
                            `;
                            tbody.appendChild(summaryRow);

                            let dataIndex = 0;
                            // Fill up to 20 rows
                            for (let i = 0; i < Math.min(26, allDates.length || 26); i++) {
                                const tr = document.createElement('tr');

                                if (allDates.length > 0) {
                                    // Check if there's attendance data for this date
                                    const currentDate = allDates[i].toISOString().split('T')[0];
                                    const attendanceData = data[dataIndex];

                                    if (attendanceData && attendanceData.date === currentDate) {
                                        // Data exists for this date
                                        tr.innerHTML = `
                                            <td style="padding: 12px; border: 1px solid #ddd;">${i + 1}</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">${escapeHtml(attendanceData.cust_uid || '')}</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">${escapeHtml(attendanceData.customer_name || '')}</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">${escapeHtml(attendanceData.date || '')} (${allDates[i].getDay() === 1 ? 'Mon' : allDates[i].getDay() === 2 ? 'Tue' : allDates[i].getDay() === 3 ? 'Wed' : allDates[i].getDay() === 4 ? 'Thu' : allDates[i].getDay() === 5 ? 'Fri' : allDates[i].getDay() === 6 ? 'Sat' : 'Sun'})</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">${escapeHtml(attendanceData.time_in ? new Date(attendanceData.time_in).toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true }) : '')}</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">${escapeHtml(attendanceData.time_out ? new Date(attendanceData.time_out).toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true }) : '')}</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">${escapeHtml(attendanceData.attendance_time ? new Date(attendanceData.attendance_time).toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true }) : '')}</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">${escapeHtml((attendanceData.vehicle_name || '').split('/')[0] || '')}</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">${escapeHtml(attendanceData.trainer_name || '')}</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">${escapeHtml(attendanceData.note || '')}</td>
                                        `;
                                        dataIndex++;
                                    } else {
                                        // No attendance on this date
                                        tr.innerHTML = `
                                            <td style="padding: 12px; border: 1px solid #ddd;">${i + 1}</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">-</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">-</td>
                                            <td style="padding: 12px; border: 1px solid #ddd; background-color: #ffe6e6;">${currentDate} (${allDates[i].getDay() === 1 ? 'Mon' : allDates[i].getDay() === 2 ? 'Tue' : allDates[i].getDay() === 3 ? 'Wed' : allDates[i].getDay() === 4 ? 'Thu' : allDates[i].getDay() === 5 ? 'Fri' : allDates[i].getDay() === 6 ? 'Sat' : 'Sun'}) (Absent)</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">-</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">-</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">-</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">-</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">-</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">Absent</td>
                                        `;
                                    }
                                } else {
                                    // Empty row when no attendance data exists
                                    tr.innerHTML = `
                                        <td style="padding: 12px; border: 1px solid #ddd;">${i + 1}</td>
                                        <td style="padding: 12px; border: 1px solid #ddd;"> </td>
                                        <td style="padding: 12px; border: 1px solid #ddd;"> </td>
                                        <td style="padding: 12px; border: 1px solid #ddd;"> </td>
                                        <td style="padding: 12px; border: 1px solid #ddd;"> </td>
                                        <td style="padding: 12px; border: 1px solid #ddd;"> </td>
                                        <td style="padding: 12px; border: 1px solid #ddd;"> </td>
                                        <td style="padding: 12px; border: 1px solid #ddd;"> </td>
                                        <td style="padding: 12px; border: 1px solid #ddd;"> </td>
                                        <td style="padding: 12px; border: 1px solid #ddd;"> </td>
                                        <td style="padding: 12px; border: 1px solid #ddd;"> </td>
                                    `;
                                }
                                tbody.appendChild(tr);
                            }
                        } catch (err) {
                            console.error('Error processing data:', err);
                            showError('Error processing attendance data: ' + err.message);
                            attendanceModal.close();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        console.error('Status:', status);
                        console.error('Response:', xhr.responseText);
                        showError('Failed to load attendance data. Please try again later.');
                        attendanceModal.close();
                    }
                });
            }

            // Helper function to escape HTML and prevent XSS
            function escapeHtml(unsafe) {
                if (unsafe == null) return '';
                return unsafe
                    .toString()
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }

            // Helper function to show error messages
            function showError(message) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message,
                    confirmButtonColor: '#3085d6'
                });
            }
        });
    </script>
</body>

</html>