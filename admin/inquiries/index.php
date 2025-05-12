    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
        <link rel="shortcut icon" type="image/png" href="../../assets/logo.png" />
        <title>Booking Inquiries</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: system-ui, -apple-system, sans-serif;
            }
            
             :root {
                --primary-color: #4F46E5;
                --header-height: 64px;
                --standard-color: hsl(210, 100%, 56%);
                --premium-color: hsl(0, 0%, 0%);
                --gold-color: hsl(46, 84%, 48%);
                --platinum-color: hsl(201, 17%, 67%);
                --emerald-color: hsl(126, 60%, 19%);
                --silver-color: hsl(201, 17%, 67%) ;
                --bg-color: #F3F4F6;
                --dark: #111827;
            }
            
            body {
                background-color: var(--bg-color);
            }

            .header {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                height: var(--header-height);
                background: linear-gradient(135deg, #1e293b, #334155);
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                z-index: 100;
                display: flex;
                align-items: center;
                padding: 0 1.5rem;
            }

            .back-button {
                background: rgba(255,255,255,0.2);
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
                background: rgba(255,255,255,0.3);
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

            .main-content {
                margin-top: var(--header-height);
                padding: 1.5rem;
                max-width: 1400px;
                margin-left: auto;
                margin-right: auto;
                background-color: var(--bg-color);
            }

            .search-section {
                background: white;
                padding: 1.5rem;
                border-radius: 12px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                margin-bottom: 1.5rem;
                border: 1px solid rgba(0,0,0,0.05);
            }

            .search-container {
                display: flex;
                gap: 1rem;
                margin-bottom: 1rem;
            }

            .search-input {
                flex-grow: 1;
                padding: 0.75rem 1rem;
                border: 1px solid #E5E7EB;
                border-radius: 8px;
                font-size: 0.875rem;
                outline: none;
                transition: border-color 0.2s;
                background-color: #F9FAFB;
            }

            .search-input:focus {
                border-color: var(--primary-color);
                background-color: white;
            }

            .filters {
                display: flex;
                gap: 0.75rem;
                flex-wrap: wrap;
            }

            .filter-btn {
                padding: 0.5rem 1rem;
                border: 1px solid #E5E7EB;
                border-radius: 6px;
                background: white;
                font-size: 0.875rem;
                cursor: pointer;
                transition: all 0.2s;
                color: #374151;
            }

            .filter-btn:hover {
                border-color: var(--primary-color);
                color: var(--primary-color);
                background: #F8FAFC;
            }

            .filter-btn.active {
                background: var(--primary-color);
                color: white;
                border-color: var(--primary-color);
            }

            .inquiries-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
                gap: 1.5rem;
            }

            .booking-card {
                background: white;
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                overflow: hidden;
                transition: transform 0.2s, box-shadow 0.2s;
                position: relative;
                border: 1px solid rgba(0,0,0,0.05);
            }

            .booking-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            }

            .card-header {
                padding: 1.25rem;
                color: white;
                position: relative;
            }
            
            .card-header.Standard {
                background: linear-gradient(135deg, var(--standard-color), hsl(210, 100%, 36%));
            }

            .card-header.Premium {
                background: linear-gradient(135deg, var(--premium-color), hsl(0, 0%, 20%));
            }

            .card-header.Gold {
                background: linear-gradient(135deg, var(--gold-color), hsl(46, 84%, 28%));
            }

            .card-header.Platinum {
                background: linear-gradient(135deg, var(--platinum-color), hsl(201, 17%, 47%));
            }

            .card-header.Emerald {
                background: linear-gradient(135deg, var(--emerald-color), hsl(126, 60%, 9%));
            }
            .card-header.Two {
                background: linear-gradient(135deg, var(--silver-color), hsl(201, 17%, 67%));
            }


            .customer-name {
                font-size: 1.125rem;
                font-weight: 600;
                margin-bottom: 0.5rem;
                padding-right: 100px;
            }

            .booking-date {
                font-size: 0.875rem;
                opacity: 0.9;
            }

            .package-badge {
                position: absolute;
                top: 1rem;
                right: 4rem;
                background: rgba(255,255,255,0.2);
                padding: 0.375rem 0.75rem;
                border-radius: 9999px;
                font-size: 0.75rem;
                backdrop-filter: blur(4px);
            }

            .card-content {
                padding: 1.25rem;
            }

            .contact-buttons {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
                gap: 0.75rem;
                margin-bottom: 1rem;
            }

            .contact-button {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.625rem;
                border-radius: 6px;
                background: #F3F4F6;
                color: #374151;
                text-decoration: none;
                transition: background-color 0.2s;
                min-width: 0; /* Prevent overflow */
            }

            .contact-button:hover {
                background: #E5E7EB;
            }

            .contact-icon {
                font-size: 1rem;
                color: var(--primary-color);
            }

            .contact-text {
                font-size: 0.875rem;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .info-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
                margin-bottom: 1rem;
            }

            .info-item {
                overflow: hidden;
            }

            .info-label {
                font-size: 0.75rem;
                color: #6B7280;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                margin-bottom: 0.25rem;
            }

            .info-value {
                font-size: 0.875rem;
                color: #111827;
                font-weight: 500;
            }

            .features-list {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
                margin-top: 1rem;
                padding-top: 1rem;
                border-top: 1px solid #E5E7EB;
            }

            .feature-tag {
                background: #F3F4F6;
                padding: 0.375rem 0.75rem;
                border-radius: 9999px;
                font-size: 0.75rem;
                color: #374151;
            }

            .unread-marker {
                position: absolute;
                top: 1rem;
                left: 1rem;
                width: 8px;
                height: 8px;
                background: #EF4444;
                border-radius: 50%;
            }

            .mark-read-btn {
                position: absolute;
                top: 1rem;
                right: 1rem;
                background: rgba(255,255,255,0.9);
                border: none;
                width: 32px;
                height: 32px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.2s;
                color: var(--primary-color);
            }

            .mark-read-btn:hover {
                background: white;
                transform: scale(1.1);
            }

            @media (max-width: 768px) {
                .header {
                    padding: 0 1rem;
                }

                .main-content {
                    padding: 1rem;
                }

                .search-container {
                    flex-direction: column;
                }

                .inquiries-grid {
                    grid-template-columns: 1fr;
                }

                .filters {
                    flex-wrap: nowrap;
                    overflow-x: auto;
                    padding-bottom: 0.5rem;
                    -webkit-overflow-scrolling: touch;
                }

                .filter-btn {
                    flex-shrink: 0;
                }

                .info-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </head>
    <body>
        <header class="header">
            <button class="back-button">
                <i class="fas fa-arrow-left"></i>
            </button>
            <h1 class="page-title">Booking Inquiries</h1>
            <img src="https://patelmotordrivingschool.com/storage/images/pmds-assets/pmds-text-pure-w-L.png" alt="PMDS Logo" class="header-logo">
        </header>

        <main class="main-content">
            <section class="search-section">
                <div class="search-container">
                    <input type="text" class="search-input" placeholder="Search by name, email, or phone...">
                </div>
                <div class="filters">
                    <button class="filter-btn active">All</button>
                    <button class="filter-btn">New</button>
                    <button class="filter-btn">Today</button>
                    <button class="filter-btn">This Week</button>
                    <button class="filter-btn">Standard</button>
                    <button class="filter-btn">Premium</button>
                    <button class="filter-btn">Gold</button>
                    <button class="filter-btn">Platinum</button>
                    <button class="filter-btn">Emerald</button>
                    <button class="filter-btn">Two Wheeler</button>
                </div>
            </section>

            <div id="bookingInquiries" class="inquiries-grid">
                <!-- Inquiries will be populated here -->
            </div>
        </main>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script>
            function getWhatsAppLink(phone) {
                const cleanPhone = phone.replace(/\D/g, '');
                return `https://wa.me/91${cleanPhone}`;
            }

            function markAsRead(id) {
                $.ajax({
                    url: 'mark-as-read.php',
                    method: 'POST',
                    data: { id: id },
                    success: function(response) {
                        try {
                            const data = typeof response === 'string' ? JSON.parse(response) : response;
                            if(data.status === 'success') {
                                $(`#booking-${id} .unread-marker`).fadeOut();
                                $(`#booking-${id} .mark-read-btn`).fadeOut();
                            } else {
                                console.error('Error marking as read:', data.message);
                            }
                        } catch(e) {
                            console.error('Error parsing response:', e);
                        }
                    },
                    error: function(error) {
                        console.error('Error marking as read:', error);
                    }
                });
            }

            function renderBookingInquiries(inquiries) {
                if (!Array.isArray(inquiries)) {
                    $('#bookingInquiries').html('<div class="error-message">No inquiries found</div>');
                    return;
                }

                const inquiriesHtml = inquiries.map((data, index) => `
                    <div id="booking-${data.id}" class="booking-card" title='${data.is_customer ? 'Customer' : 'Inquiry'}' data-is-customer='${data.is_customer}' ${data.is_customer ? 'style="border: 2px solid #4CAF50; box-shadow: 0 0 8px rgba(76, 175, 80, 0.3);" ' : ''}>
                        ${!data.is_read ? '<div class="unread-marker"></div>' : ''}
                        <div class="card-header ${data.package_name ? data.package_name.split(' ')[0] : ''}">
                            <div class="package-badge">${data.package_name || 'N/A'}</div>
                            <div class="customer-name">${data.name || 'Unknown'}</div>
                            <div class="booking-date">${data.booking_inquiry_date ? moment(data.booking_inquiry_date).format('MMM D, YYYY') : 'N/A'}</div>
                            ${!data.is_read ? `
                                <button onclick="markAsRead(${data.id})" class="mark-read-btn">
                                    <i class="fas fa-check"></i>
                                </button>
                            ` : ''}
                        </div>
                        <div class="card-content">
                            <div class="contact-buttons">
                                <a href="mailto:${data.email || ''}" class="contact-button">
                                    <i class="fas fa-envelope contact-icon"></i>
                                    <span class="contact-text">${data.email || 'N/A'}</span>
                                </a>
                                <a href="${getWhatsAppLink(data.phone || '')}" target="_blank" class="contact-button">
                                    <i class="fab fa-whatsapp contact-icon"></i>
                                    <span class="contact-text">${data.phone || 'N/A'}</span>
                                </a>
                                   <a href="tel:${data.phone || ''}" class="contact-button">
                                    <i class="fas fa-phone contact-icon"></i>
                                    <span class="contact-text">Call</span>
                                </a>
                                <a href="../admissionForm.php?id=${data.id}&name=${encodeURIComponent(data.name)}&phone=${encodeURIComponent(data.phone)}&email=${encodeURIComponent(data.email)}&distance=${encodeURIComponent(data.distance.split(' ')[0])}&totalA=${encodeURIComponent(data.package_price ? Math.floor(data.package_price.replace(/[₹,]/g, '')) : '')}&days=${encodeURIComponent(data.duration.split(' ')[0])}" class="contact-button">
                                    <i class="fas fa-user-plus contact-icon"></i>
                                    <span class="contact-text">Admit</span>
                                </a>
                            </div>
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">Package Price</div>
                                    <div class="info-value">${data.package_price || 'N/A'}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Duration</div>
                                    <div class="info-value">${data.duration || 'N/A'}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Vehicle</div>
                                    <div class="info-value">${data.vehicle_name ? `${data.vehicle_name} (${data.vehicle_type || 'N/A'})` : 'N/A'}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Time Slot</div>
                                    <div class="info-value">${data.time_slot || 'N/A'}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Distance</div>
                                    <div class="info-value">${data.distance || 'N/A'}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Session Duration</div>
                                    <div class="info-value">${data.session_duration || 'N/A'}</div>
                                </div>
                            </div>
                            <div class="features-list">
                                ${Array.isArray(data.package_features) ? data.package_features.map(feature => 
                                    `<span class="feature-tag">${feature}</span>`
                                ).join('') : ''}
                            </div>
                        </div>
                    </div>
                `).join('');

                $('#bookingInquiries').html(inquiriesHtml || '<div class="error-message">No inquiries found</div>');
            }

            function fetchInquiries(searchTerm = '', filterType = 'All') {
                let params = new URLSearchParams();
                if(searchTerm) params.append('search', searchTerm);
                if(filterType !== 'All') params.append('filter', filterType);

                $.ajax({
                    url: 'fetch-inquiries.php?' + params.toString(),
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        try {
                            const data = typeof response === 'string' ? JSON.parse(response) : response;
                            if(data.status === 'success' && data.data) {
                                renderBookingInquiries(data.data);
                            } else {
                                console.error('Error fetching inquiries:', data.message);
                                $('#bookingInquiries').html('<div class="error-message">No inquiries found</div>');
                            }
                        } catch(e) {
                            console.error('Error parsing response:', e);
                            $('#bookingInquiries').html('<div class="error-message">Error loading inquiries</div>');
                        }
                    },
                    error: function(error) {
                        console.error('Error fetching inquiries:', error);
                        $('#bookingInquiries').html('<div class="error-message">Error loading inquiries</div>');
                    }
                });
            }

            $(document).ready(function() {
                // Initial fetch
                fetchInquiries();

                // Search functionality
                let searchTimeout;
                $('.search-input').on('input', function() {
                    const searchTerm = $(this).val();
                    const activeFilter = $('.filter-btn.active').text();
                    
                    // Clear existing timeout
                    clearTimeout(searchTimeout);
                    
                    // Set new timeout
                    searchTimeout = setTimeout(() => {
                        fetchInquiries(searchTerm, activeFilter);
                    }, 300); // Debounce for 300ms
                });

                // Filter functionality
                $('.filter-btn').click(function() {
                    $('.filter-btn').removeClass('active');
                    $(this).addClass('active');
                    const filterType = $(this).text();
                    const searchTerm = $('.search-input').val();
                    fetchInquiries(searchTerm, filterType);
                });

                // Back button functionality
                $('.back-button').click(function() {
                    window.history.back();
                });
            });
        </script>
    </body>
    </html>