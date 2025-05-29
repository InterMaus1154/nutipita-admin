<style>
    *{
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        margin: 0;
        font-family: 'Roboto', sans-serif;
    }

    .invoice-wrapper {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    header {
        background-color: #323332;
        padding: 2rem 1rem;
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
        color: #fff;
    }

    .logo-wrapper{
        max-width: 150px;
    }

    img{
        max-width: 100%;
    }

    .invoice-middle {
        text-transform: uppercase;
        color: #fff;
        font-size: 2rem;
        letter-spacing: 1px;
    }

    .company-data{
        display: flex;
        gap: 1rem;
        flex-direction: column;
        text-align: right;
        align-items: flex-end;

        strong{
            font-size: 1.5rem;
        }

        p{
            font-size: .9rem;
            line-height: 1.4;
        }
    }

</style>
<div class="invoice-wrapper">
    <header>
        @php
            $logo = public_path('images/logo.png');
        @endphp
        <div class="logo-wrapper">
            @inlinedImage($logo)
        </div>
        <div class="invoice-middle">
            INVOICE
        </div>
        <div class="company-data">
            <strong>Nuti Pita Limited</strong>
            <p>
                Unit 13, Langhedge Industrial Estate
                <br>
                London Enfield N18 2TQ
                <br>
                United Kingdom
            </p>
            <p>
                +44 7754 22632
                <br>
                nutipita.co.uk
            </p>
            <p>
                VAT: 486 4163 64
            </p>
        </div>
    </header>
    <main>

    </main>
    <footer>

    </footer>
</div>
