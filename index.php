<?php include_once("header.php") ?>

    <style>
        .top-box {
            background-color: var(--bs-gray-300);
            border-radius: 10px;
            padding: 40px 20px;
            text-align: center;
        }

        .section-box {
            background-color: var(--bs-gray-200);
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }

        .product-card {
            background-color: white;
            border: 1px solid var(--bs-gray-400);
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            transition: box-shadow .2s ease;
        }

        .product-card:hover {
            box-shadow: 0px 0px 10px rgba(0,0,0,0.2);
        }
    </style>

    <div class="container mt-4">

        <div class="top-box">
            <h1>EpicAwesomeStore</h1>
            <p>The best one-stop online shop for all your epic, awesome needs.</p>
        </div>

        <div class="section-box">
            <h3 class="text-center">Featured Products</h3>

            <div class="row mt-3">

                <div class="col-md-4 mb-3">
                    <div class="product-card">
                        <h5>Lego Death Star</h5>
                        <p>Once the dream present for all kids, now the next thing on your shopping list.</p>
                        <button class="btn btn-primary btn-sm">View</button>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="product-card">
                        <h5>Used Pants</h5>
                        <p>Heavily worn, not comfortable or fashionable at all.</p>
                        <button class="btn btn-primary btn-sm">View</button>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="product-card">
                        <h5>Crow Bar</h5>
                        <p>It's a crow bar, what else do you want? *Do not use for illegal purposes</p>
                        <button class="btn btn-primary btn-sm">View</button>
                    </div>
                </div>

            </div>
            <div class="row mt-3">

                <div class="col-md-4 mb-3">
                    <div class="product-card">
                        <h5>Target Ball</h5>
                        <p>Have you ever wanted the big, red, concrete ball outside our store? well now you can have it!</p>
                        <button class="btn btn-primary btn-sm">View</button>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="product-card">
                        <h5>Pokémon Booster Pack</h5>
                        <p>Booster pack containing 10 new cards for your Pokémon collection</p>
                        <button class="btn btn-primary btn-sm">View</button>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="product-card">
                        <h5>Queen Size Fluffy Plush Blanket</h5>
                        <p>High Quality Fluffy Plush Blanket - perfect for cold nights & watching movies with </p>
                        <button class="btn btn-primary btn-sm">View</button>
                    </div>
                </div>

            </div>
        </div>

    </div>
    <div>
<?php include_once("footer.php")?>