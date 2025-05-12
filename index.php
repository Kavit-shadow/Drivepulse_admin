<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DrivePulse - Driving School Management System</title>
    <link rel="manifest" href="./manifest.json">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1a1a1a, #2d2d2d);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background-color: rgba(20, 20, 20, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
            position: fixed;
            width: 100%;
            box-sizing: border-box;
            z-index: 1000;
        }

        .navbar ul {
            display: flex;
            justify-content: center;
            align-items: center;
            list-style: none;
            margin: 0;
            padding: 0;
            max-width: 1200px;
            margin: 0 auto;
        }

        .navbar ul li {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .navbar ul li .x1 {
            color: #fff;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .navbar ul li .x1:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .button-container {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            align-items: center;
        }

        .login-btn, .install-btn {
            background: linear-gradient(45deg, #46abcc, #3d91ad);
            color: white;
            padding: 1rem 2.5rem;
            border: none;
            border-radius: 30px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(70, 171, 204, 0.3);
            position: relative;
            overflow: hidden;
        }

        .login-btn:before, .install-btn:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                120deg,
                transparent,
                rgba(255, 255, 255, 0.3),
                transparent
            );
            transition: 0.5s;
        }

        .login-btn:hover:before, .install-btn:hover:before {
            left: 100%;
        }

        .login-btn:hover, .install-btn:hover {
            background: linear-gradient(45deg, #3d91ad, #46abcc);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(70, 171, 204, 0.5);
        }

        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            text-align: center;
            color: white;
            margin-top: 80px;
            background: radial-gradient(circle at center, rgba(70, 171, 204, 0.1) 0%, transparent 70%);
            position: relative;
        }

        .main-content:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.05;
            pointer-events: none;
        }

        .main-content h1 {
            font-size: 4.5rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(45deg, #46abcc, #6dd5ed);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
            animation: glow 2s ease-in-out infinite alternate;
            letter-spacing: -1px;
        }

        .main-content p {
            font-size: 1.4rem;
            color: #e0e0e0;
            max-width: 800px;
            margin: 0 auto 3.5rem;
            line-height: 1.7;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
            opacity: 0.9;
        }

        @keyframes glow {
            from {
                text-shadow: 0 0 10px rgba(70, 171, 204, 0.5);
            }
            to {
                text-shadow: 0 0 20px rgba(70, 171, 204, 0.8),
                             0 0 30px rgba(70, 171, 204, 0.6);
            }
        }

        .logo-img {
            height: 50px;
            margin-right: 20px;
            transition: transform 0.3s ease;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
        }

        .logo-img:hover {
            transform: scale(1.05) rotate(-2deg);
        }

        @media (max-width: 768px) {
            .main-content h1 {
                font-size: 2.8rem;
                margin-bottom: 1rem;
            }
            .main-content p {
                font-size: 1.2rem;
                padding: 0 1.5rem;
                margin-bottom: 2.5rem;
            }
            .login-btn, .install-btn {
                padding: 0.9rem 2.2rem;
                font-size: 1rem;
            }
            .navbar {
                padding: 0.8rem 1.5rem;
            }
            .logo-img {
                height: 40px;
            }
            .button-container {
                flex-direction: column;
                gap: 1rem;
            }
        }

        @media (max-width: 480px) {
            .main-content h1 {
                font-size: 2.2rem;
            }
            .main-content p {
                font-size: 1.1rem;
                padding: 0 1rem;
            }
        }

    </style>
    <link rel="shortcut icon" type="image/png" href="assets/logo.png"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="navbar">
        <ul>
            <li>
                <img src="assets/name.png" alt="PMDS Logo" class="logo-img">
            </li>
        </ul>
    </div>
    <div class="main-content">
        <div class="welcome-container">
            <h1>Welcome to DrivePulse</h1>
            <p>Transform your driving school management with our comprehensive solution. Experience seamless operations, 
            efficient attendance tracking, and accelerated business growth through our innovative platform.</p>
            <div class="button-container">
                <a href="login_form.php" class="login-btn">Access Dashboard</a>
                <button id="install-button" class="install-btn">Install App</button>
            </div>
        </div>
    </div>

<style>
    .cursor-dot,
    .cursor-dot-outline {
        pointer-events: none;
        position: fixed;
        top: 0;
        left: 0;
        border-radius: 50%;
        opacity: 0;
        transform: translate(-50%, -50%);
        transition: opacity 0.3s ease-in-out;
        z-index: 9999;
    }

    .cursor-dot {
        width: 8px;
        height: 8px;
        background-color: #2196F3;
    }

    .cursor-dot-outline {
        width: 40px;
        height: 40px;
        background-color: rgba(33, 150, 243, 0.2);
    }

    * {
        cursor: none !important;
    }

    a, button {
        cursor: none;
    }
</style>

<div class="cursor-dot"></div>
<div class="cursor-dot-outline"></div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .swal2-popup {
        font-size: 1rem;
        font-family: inherit;
    }

    .swal2-title {
        font-size: 1.5rem;
        font-weight: 600;
    }

    .swal2-content {
        font-size: 1rem;
    }

    .swal2-confirm,
    .swal2-cancel {
        font-size: 1rem !important;
        padding: 0.5rem 1.5rem;
    }

    .swal2-popup.swal2-toast {
        font-size: 0.875rem;
    }
</style>


<script>
document.addEventListener('DOMContentLoaded', () => {
    // Check if device is desktop (no touch and wider screen)
    const isDesktop = !('ontouchstart' in window) && window.innerWidth > 768;

    const cursorDot = document.querySelector('.cursor-dot');
    const cursorOutline = document.querySelector('.cursor-dot-outline');

    if (isDesktop) {
        // Show custom cursor for desktop
        let cursorVisible = false;
        let cursorEnlarged = false;

        // Show cursor on mouse move
        document.addEventListener('mousemove', (e) => {
            if (!cursorVisible) {
                cursorDot.style.opacity = 1;
                cursorOutline.style.opacity = 1;
                cursorVisible = true;
            }

            // Position the dot cursor immediately
            cursorDot.style.transform = `translate(${e.clientX}px, ${e.clientY}px)`;

            // Position the outline cursor with delay
            setTimeout(() => {
                cursorOutline.style.transform = `translate(${e.clientX}px, ${e.clientY}px)`;
            }, 150);
        });

        // Hide cursor when leaving window
        document.addEventListener('mouseout', () => {
            cursorDot.style.opacity = 0;
            cursorOutline.style.opacity = 0;
            cursorVisible = false;
        });

        // Enlarge cursor on clickable elements
        document.addEventListener('mouseover', (e) => {
            if (e.target.tagName.toLowerCase() === 'a' || 
                e.target.tagName.toLowerCase() === 'button' ||
                e.target.classList.contains('login-btn')) {
                cursorEnlarged = true;
                cursorDot.style.transform = `translate(${e.clientX}px, ${e.clientY}px) scale(1.5)`;
                cursorOutline.style.transform = `translate(${e.clientX}px, ${e.clientY}px) scale(1.5)`;
                cursorOutline.style.backgroundColor = 'rgba(33, 150, 243, 0.4)';
            }
        });

        // Return cursor to normal size
        document.addEventListener('mouseout', (e) => {
            if (e.target.tagName.toLowerCase() === 'a' || 
                e.target.tagName.toLowerCase() === 'button' ||
                e.target.classList.contains('login-btn')) {
                cursorEnlarged = false;
                cursorDot.style.transform = `translate(${e.clientX}px, ${e.clientY}px) scale(1)`;
                cursorOutline.style.transform = `translate(${e.clientX}px, ${e.clientY}px) scale(1)`;
                cursorOutline.style.backgroundColor = 'rgba(33, 150, 243, 0.2)';
            }
        });

        // Prevent text selection
        document.addEventListener('selectstart', (e) => {
            e.preventDefault();
        });

        // Show cursor elements
        cursorDot.style.display = 'block';
        cursorOutline.style.display = 'block';

        // Remove default cursor for desktop
        document.querySelectorAll('*').forEach(element => {
            element.style.cursor = 'none';
        });

    } else {
        // Hide custom cursor elements for mobile/touch
        cursorDot.style.display = 'none';
        cursorOutline.style.display = 'none';

        // Restore default cursors
        document.querySelectorAll('*').forEach(element => {
            element.style.removeProperty('cursor');
        });
        document.querySelectorAll('a, button').forEach(element => {
            element.style.cursor = 'pointer';
        });
    }
});
</script>




<script>
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('./sw.js')
    .then(() => console.log('Service Worker registered'))
    .catch(error => console.error('Service Worker registration failed:', error));
}

let deferredPrompt;
const installButton = document.querySelector('#install-button');

// Hide install button by default
installButton.style.display = 'none';

// Check if app is already installed
if (window.matchMedia('(display-mode: standalone)').matches || 
    window.navigator.standalone === true) {
  // App is installed, keep button hidden
  installButton.style.display = 'none';
} else {
  // Listen for install prompt
  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;

    // Show the install button
    installButton.style.display = 'block';

    // Show install popup after 3 seconds
    // Only show install prompt if app is not installed
    if (!window.matchMedia('(display-mode: standalone)').matches && 
        window.navigator.standalone !== true) {
      setTimeout(() => {
        Swal.fire({
          title: 'Install DrivePulse App',
          text: 'Install our app for a better experience!', 
          icon: 'info',
          showCancelButton: true,
          confirmButtonText: 'Install Now',
          cancelButtonText: 'Maybe Later',
          confirmButtonColor: '#2196F3'
        }).then((result) => {
          if (result.isConfirmed) {
            // Show loading state
            installButton.innerHTML = '<div class="loader"></div>';
            installButton.disabled = true;

            deferredPrompt.prompt();
            deferredPrompt.userChoice.then(choiceResult => {
              if (choiceResult.outcome === 'accepted') {
                console.log('User accepted the install prompt');
                Swal.fire('Success!', 'App installation started', 'success');
                installButton.style.display = 'none';
              } else {
                console.log('User dismissed the install prompt');
                Swal.fire('Cancelled', 'App installation cancelled', 'info');
                installButton.innerHTML = 'Install App';
                installButton.disabled = false;
              }
              deferredPrompt = null;
            });
          }
        });
      }, 3000);
    }

    installButton.addEventListener('click', () => {
      // Show loading state  
      installButton.innerHTML = '<div class="loader"></div>';
      installButton.disabled = true;

      deferredPrompt.prompt();
      deferredPrompt.userChoice.then(choiceResult => {
        if (choiceResult.outcome === 'accepted') {
          console.log('User accepted the install prompt');
          Swal.fire('Success!', 'App installation started', 'success');
          installButton.style.display = 'none';
        } else {
          console.log('User dismissed the install prompt');
          Swal.fire('Cancelled', 'App installation cancelled', 'info');
          installButton.innerHTML = 'Install App';
          installButton.disabled = false;
        }
        deferredPrompt = null;
      });
    });
  });
}

// Add loader styles
const style = document.createElement('style');
style.textContent = `
  .loader {
    width: 20px;
    height: 20px;
    border: 3px solid #ffffff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 1s linear infinite;
    margin: 0 auto;
  }
  
  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
  }
`;
document.head.appendChild(style);
</script>



</body>

</html>
