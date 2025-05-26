<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('../auth/login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Home - Event Management</title>
<style>
  /* Reset and base */
  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }
  html, body {
    height: 100%;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #eee;
    line-height: 1.6;
    background: url('https://images.squarespace-cdn.com/content/v1/57b9b98a29687f1ef5c622df/1481560170009-3A9N8U2LZA770OJAS6W5/0000000.jpg') no-repeat center center fixed;
    background-size: cover;
  }
  /* Overlay for better text readability */
  body::before {
    content: "";
    position: fixed;
    top:0; left:0; right:0; bottom:0;
    background: rgba(0,0,0,0.55);
    z-index: -1;
  }
  /* Navbar */
  nav.navbar {
    background: rgba(30, 60, 114, 0.85); /* translucent deep navy */
    box-shadow: 0 4px 10px rgba(0,0,0,0.6);
    padding: 1rem 2rem;
    position: sticky;
    top: 0;
    z-index: 100;
  }
  nav.navbar ul {
    list-style: none;
    display: flex;
    justify-content: center;
    gap: 2rem;
  }
  nav.navbar a {
    color: #fbc531;
    font-weight: 600;
    text-transform: uppercase;
    text-decoration: none;
    letter-spacing: 0.08em;
    font-size: 0.9rem;
    transition: color 0.3s, border-bottom 0.3s;
    padding-bottom: 0.25rem;
  }
  nav.navbar a:hover,
  nav.navbar a:focus {
    color: #fff;
    border-bottom: 2px solid #fff;
    outline: none;
  }

  /* Hero Section */
  .home-container {
    max-width: 900px;
    margin: 6rem auto 3rem;
    background: rgba(0, 0, 0, 0.65);
    border-radius: 12px;
    padding: 3rem 2rem;
    box-shadow: 0 10px 25px rgba(0,0,0,0.8);
    text-align: center;
  }
  .home-container h2 {
    font-size: 3rem;
    color: #fbc531;
    margin-bottom: 0.75rem;
    font-weight: 800;
    text-shadow: 0 2px 7px rgba(0,0,0,0.6);
  }
  .home-container p {
    font-size: 1.3rem;
    color: #ddd;
    margin-bottom: 3rem;
    text-shadow: 0 1px 5px rgba(0,0,0,0.6);
  }

  /* Footer styles from create_event.php */
  footer#footer {
      background: rgba(30, 60, 114, 0.9);
      color: #eee;
      padding: 2rem 1rem;
      margin-top: 4rem;
  }
  .footer-container {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      gap: 2rem;
  }
  .footer-section {
      flex: 1 1 250px;
      min-width: 220px;
  }
  .footer-section h2, .footer-section h3 {
      color: #fbc531;
      margin-bottom: 0.75rem;
  }
  .footer-bottom {
      text-align: center;
      margin-top: 1.5rem;
      font-size: 0.9rem;
      color: #bbb;
  }

  /* Responsive */
  @media (max-width: 650px) {
    nav.navbar ul {
      flex-direction: column;
      gap: 1rem;
    }
    .footer-container {
      flex-direction: column;
      gap: 1.5rem;
    }
  }
</style>
</head>
<body>
  <nav class="navbar" role="navigation">
    <ul>
      <li><a href="home.php">Home</a></li>
      <li><a href="events.php">Events</a></li>
      <li><a href="create_event.php">Create Event</a></li>
      <li><a href="reservations.php">My Reservations</a></li>
      <li><a href="support.php">Support</a></li>
      <li><a href="#footer">About Us</a></li>
      <li><a href="../auth/logout.php">Logout</a></li>
    </ul>
  </nav>

  <main class="home-container" role="main" aria-label="Welcome and introduction">
    <h2>Welcome to Tirana Unplugged !</h2>
    <h3>Create your FREE event website today<h3/>
  </main>

  <footer id="footer">
    <div class="footer-container">
        <div class="footer-section">
            <h2>Tirana Unplugged</h2>
            <p>Tirana Unplugged është një platformë online që mundëson përdoruesve të rezervojnë dhe krijojnë evente në qytetin e Tiranës. Me një dizajn të thjeshtë dhe intuitiv, përdoruesit mund të eksplorojnë mundësitë për ngjarje të ndryshme dhe të organizojnë aktivitetet e tyre të preferuara.</p>
        </div>
        <div class="footer-section">
            <h3>Contact</h3>
            <p>+355 69 558 6969</p>
            <p>makinaime@gmail.com</p>
            <p>Rruga e Elbasanit, Tirana, Albania</p>
        </div>
        <div class="footer-section">
            <h3>Social Media</h3>
            <p>TikTok</p>
            <p>Instagram</p>
            <p>Facebook</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>Copyright 2025. All rights reserved.</p>
    </div>
</footer>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const aboutUsLink = document.querySelector('a[href="#footer"]');
      if (aboutUsLink) {
        aboutUsLink.addEventListener('click', (e) => {
          e.preventDefault();
          document.getElementById('footer').scrollIntoView({behavior: 'smooth'});
        });
      }
    });
  </script>
</body>
</html>


