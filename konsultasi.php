<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Mastery</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <header>
        <a href="index.php" class="logo">Money Mastery</a>
        <nav class="navigation">
            <a href="tips.php">Tips</a>
            <a href="konsultasi.php">Konsultasi</a>
            <a href="about.php">Tentang</a>
            <a href="bantuan.php">Bantuan</a>
        </nav>
    </header>

    <main>
        <div class="container">
            <section class="faq-section">
            <h1 class="section-title">Konsultasi Money Mastery</h1>
                <p class="about-description">Anda memiliki pertanyaan atau masalah tentang pengelolaan keuangan? Kami siap membantu Anda!</p>
                <br></br>
                <div class="contact-methods">
                    <div class="contact-item">
                        <h3>Hubungi Kami</h3>
                        <p>Jika Anda mengalami masalah atau memiliki pertanyaan, jangan ragu untuk menghubungi kami!</p>
                        <p>Nomor Telepon: <a href="https://wa.me/089123123123">089123123123</a></p>
                    </div>
                    
                    <div class="contact-item">
                        <h3>Email</h3>
                        <p>Anda juga bisa mengirim email ke:</p>
                        <p>Email: <a href="mailto:support@moneymastery.com">support@moneymastery.com</a></p>
                    </div>
                    
                    <div class="contact-item">
                        <h3>Formulir Kontak</h3>
                        <p>Atau isi formulir kontak di bawah ini dan kami akan menghubungi Anda secepatnya:</p>
                        <form id="contactForm">
                            <label for="name">Nama:</label>
                            <input type="text" id="name" name="name" required autocomplete="off">
                            
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" required autocomplete="off">
                            
                            <label for="message">Pesan:</label>
                            <textarea id="message" name="message" rows="4" required autocomplete="off"></textarea>
                            
                            <button type="submit">Kirim</button>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </main>
    
    <footer>
        <p>&copy; 2024 Money Mastery. All rights reserved.</p>
    </footer>

    <script>
        document.getElementById('contactForm').addEventListener('submit', function(event) {
            event.preventDefault();
            alert('Pesan Terkirim');
            document.getElementById('contactForm').reset();
        });
    </script>
</body>
</html>
