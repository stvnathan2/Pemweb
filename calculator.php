<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discount Calculator</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <a href="home.php" class="logo">Money Mastery</a>
        <nav class="navigation">
            <a href="#tips">Tips</a>
            <a href="#konsultasi">Konsultasi</a>
            <a href="#tentang">Tentang</a>
            <a href="#bantuan">Bantuan</a>
        </nav>
    </header>

    <div class="container mt-5">
        <form method="post" action="" class="form-inline" oninput="calculateAndDisplayTotal()">
            <div class="form-row">
                <div class="inline-form-group col-md-4">
                    <label for="price">Harga (Rp):</label>
                    <input type="text" class="form-control" id="price" name="price" autocomplete="off" onclick="setActiveInput('price')" onkeypress="return onlyNumberKey(event)" required>
                </div>
                <div class="inline-form-group col-md-4">
                    <label for="discount">Diskon:</label>
                    <input type="text" class="form-control" id="discount" name="discount" autocomplete="off" onclick="setActiveInput('discount')" onkeypress="return onlyNumberKey(event)" required>
                </div>
                <div class="inline-form-group col-md-4">
                    <label for="taxRate">Pajak:</label>
                    <input type="text" class="form-control" id="taxRate" name="taxRate" autocomplete="off" onclick="setActiveInput('taxRate')" onkeypress="return onlyNumberKey(event)" required>
                </div>
            </div>
        </form>
        <div class='alert alert-success mt-4' role='alert' id="totalPaymentDisplay">Total pembayaran: Rp0</div>

        <div class="calculator">
            <button class="btn btn-secondary" onclick="clearDisplay()">C</button>
            <button class="btn btn-secondary" onclick="backspace()">←</button>
            <button class="btn btn-secondary" onclick="appendToDisplay('%')">%</button>
            <button class="btn btn-secondary" onclick="appendToDisplay('/')">/</button>
            <button class="btn btn-secondary" onclick="appendToDisplay('7')">7</button>
            <button class="btn btn-secondary" onclick="appendToDisplay('8')">8</button>
            <button class="btn btn-secondary" onclick="appendToDisplay('9')">9</button>
            <button class="btn btn-secondary" onclick="appendToDisplay('*')">*</button>
            <button class="btn btn-secondary" onclick="appendToDisplay('4')">4</button>
            <button class="btn btn-secondary" onclick="appendToDisplay('5')">5</button>
            <button class="btn btn-secondary" onclick="appendToDisplay('6')">6</button>
            <button class="btn btn-secondary" onclick="appendToDisplay('-')">-</button>
            <button class="btn btn-secondary" onclick="appendToDisplay('1')">1</button>
            <button class="btn btn-secondary" onclick="appendToDisplay('2')">2</button>
            <button class="btn btn-secondary" onclick="appendToDisplay('3')">3</button>
            <button class="btn btn-secondary" onclick="appendToDisplay('+')">+</button>
            <button class="btn btn-secondary" onclick="appendToDisplay(',')">,</button>
            <button class="btn btn-secondary" onclick="appendToDisplay('0')">0</button>
            <button class="btn btn-primary" style="grid-column: span 2;" onclick="calculateResult()">=</button>
        </div>
    </div>

    <script>
        let activeInput = '';

        function setActiveInput(inputId) {
            activeInput = inputId;
        }

        function appendToDisplay(value) {
            if (activeInput) {
                let inputElement = document.getElementById(activeInput);
                inputElement.value += value;
                calculateAndDisplayTotal();
            }
        }

        function clearDisplay() {
            if (activeInput) {
                document.getElementById(activeInput).value = '';
                calculateAndDisplayTotal();
            }
        }

        function backspace() {
            if (activeInput) {
                let inputElement = document.getElementById(activeInput);
                inputElement.value = inputElement.value.slice(0, -1);
                calculateAndDisplayTotal();
            }
        }

        function calculateResult() {
            if (activeInput) {
                let inputElement = document.getElementById(activeInput);
                try {
                    inputElement.value = eval(inputElement.value);
                } catch (error) {
                    inputElement.value = 'Error';
                }
                calculateAndDisplayTotal();
            }
        }

        function calculateTotal() {
            const price = parseFloat(document.getElementById('price').value.replace(/\./g, '').replace(/,/g, '.')) || 0;
            const discount = parseFloat(document.getElementById('discount').value) || 0;
            const taxRate = parseFloat(document.getElementById('taxRate').value) || 0;

            const discountAmount = (discount / 100) * price;
            const discountedTotal = price - discountAmount;
            const taxAmount = (taxRate / 100) * discountedTotal;
            const finalTotal = discountedTotal + taxAmount;

            return finalTotal;
        }

        function calculateAndDisplayTotal() {
            const total = calculateTotal();
            document.getElementById('totalPaymentDisplay').innerText = `Total pembayaran: Rp${total.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        }

        function onlyNumberKey(evt) {
            let ASCIICode = (evt.which) ? evt.which : evt.keyCode;
            if (
                (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57)) &&
                ASCIICode !== 43 &&
                ASCIICode !== 45 &&
                ASCIICode !== 42 &&
                ASCIICode !== 47 &&
                ASCIICode !== 37 &&
                ASCIICode !== 44
            ) {
                return false;
            }
            return true;
        }

        document.getElementById('price').addEventListener('input', function () {
            calculateAndDisplayTotal();
        });

        document.getElementById('discount').addEventListener('input', function () {
            calculateAndDisplayTotal();
        });

        document.getElementById('taxRate').addEventListener('input', function () {
            calculateAndDisplayTotal();
        });
    </script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
