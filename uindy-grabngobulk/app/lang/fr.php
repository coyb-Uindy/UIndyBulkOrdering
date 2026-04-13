<?php
// lang/fr.php — French / Français strings
return [
    // ── Navigation ──────────────────────────────────────────
    'nav_title'         => 'Commande en Vrac Grab-N-Go',
    'nav_subtitle'      => 'Université d\'Indianapolis',
    'language'          => '🌐 Langue',
    'admin_login'       => '🔐 Connexion Admin',
    'sign_out'          => '🚪 Déconnexion',
    'my_order'          => '📦 Ma Commande',

    // ── Hero ────────────────────────────────────────────────
    'hero_title'        => 'Pré-commande de Boissons en Vrac',
    'hero_desc'         => 'Sélectionnez une caisse ci-dessous et votre commande sera prête au Grab-N-Go avant votre arrivée — sans faire la queue ! Des rabais approuvés par la restauration s\'appliquent aux commandes éligibles.',

    // ── Menu ────────────────────────────────────────────────
    'section_label'     => 'Boissons Disponibles',
    'order_btn'         => 'Commander',
    'out_of_stock'      => 'Rupture de Stock',
    'opt_singular'      => 'option',
    'opt_plural'        => 'options',

    // ── Confirmation Modal ──────────────────────────────────
    'modal_title'       => 'Confirmer la Commande en Vrac',
    'modal_subtitle'    => 'Veuillez vérifier votre sélection avant de soumettre.',
    'case_cost'         => 'Coût de la Caisse',
    'pack_amount'       => 'Quantité par Paquet',
    'category_label'    => 'Catégorie',
    'discount_coming'   => '✨ Réduction restauration et total réduit — bientôt disponible',
    'cancel_btn'        => 'Annuler',
    'confirm_btn'       => 'Oui, Passer la Commande',
    'placing_order'     => 'Commande en cours…',

    // ── Order Status Page ───────────────────────────────────
    'status_page_title'     => 'Mes Commandes — UIndy Grab-N-Go',
    'status_order_for'      => 'Commande pour',
    'no_orders_title'       => 'Aucune Commande',
    'no_orders_desc'        => 'Vous n\'avez pas encore passé de commande. Retournez au menu pour commencer !',
    'orders_heading'        => 'Vos Commandes',
    'pack_of'               => 'Lot de',

    'status_pending_title'  => 'En Cours de Préparation',
    'status_pending_badge'  => '⏳ En attente',
    'status_complete_title' => 'Prête à Récupérer ! 🎉',
    'status_complete_badge' => '✅ Complète',
    'status_complete_msg'   => 'Rendez-vous au Grab-N-Go — votre commande est prête. Pas besoin de faire la queue !',
    'status_denied_title'   => 'Commande Indisponible',
    'status_denied_badge'   => '❌ Refusée',
    'status_denied_msg'     => 'Votre commande a malheureusement été refusée — l\'article peut être en rupture de stock. Retournez au menu pour choisir un autre article.',

    'back_to_menu'          => '← Retour au Menu',
    'refresh_now'           => '🔄 Actualiser',
    'auto_refresh_note'     => 'Mise à jour toutes les 30 secondes.',

    // ── Category names ──────────────────────────────────────
    'cat_names' => [
        'Sodas & Water'   => 'Sodas et Eau',
        'Tropicana Juice' => 'Jus Tropicana',
        'Pure Leaf Tea'   => 'Thé Pure Leaf',
        'Propel'          => 'Propel',
        'Muscle Milk'     => 'Muscle Milk',
        'Rockstar'        => 'Rockstar',
        'Starbucks'       => 'Starbucks',
        '16oz Celsius'    => 'Celsius 16oz',
        '12oz Celsius'    => 'Celsius 12oz',
        'Gatorade'        => 'Gatorade',
        'Alani'           => 'Alani',
    ],

    // ── Full item name translations ─────────────────────────
    // Checked first in translate_item(). Brand names kept as-is.
    // Proper French grammar applied (adjective placement, prepositions, etc.)
    'item_names' => [
        // ── Sodas & Water ──
        'Aquafina'              => 'Aquafina',
        'Lifewater'             => 'Lifewater',
        'Pepsi'                 => 'Pepsi',
        'Diet Pepsi'            => 'Pepsi Light',
        'Pepsi Zero'            => 'Pepsi Zéro',
        'Pepsi Cherry'          => 'Pepsi Cerise',
        'Dr. Pepper'            => 'Dr Pepper',
        'Diet Dr. Pepper'       => 'Dr Pepper Light',
        'Mtn Dew'               => 'Mountain Dew',
        'Diet Mtn Dew'          => 'Mountain Dew Light',
        'Mtn Dew Baja Blast'    => 'Mountain Dew Baja Blast',
        'Starry'                => 'Starry',
        'Crush Orange'          => 'Crush Orange',
        'Crush Grape'           => 'Crush Raisin',
        'Schweppes Ginger Ale'  => 'Schweppes Ginger Ale',
        'Root Beer'             => 'Bière de Racine',

        // ── Tropicana Juice ──
        'Apple Juice'           => 'Jus de Pomme',
        'Peach'                 => 'Pêche',
        'Orange Juice'          => 'Jus d\'Orange',
        'Cranberry'             => 'Canneberge',
        'Raspberry Lemonade'    => 'Limonade à la Framboise',
        'Strawberry Lemonade'   => 'Limonade à la Fraise',

        // ── Pure Leaf Tea ──
        'Extra Sweet Tea'       => 'Thé Extra Sucré',
        'Sweet Tea'             => 'Thé Sucré',
        'Unsweetened Tea'       => 'Thé Non Sucré',
        'Raspberry Tea'         => 'Thé à la Framboise',
        'Zero Sugar Sweet Tea'  => 'Thé Sucré Zéro Sucre',
        'Black Cherry Tea'      => 'Thé à la Cerise Noire',
        'Lemon Tea'             => 'Thé au Citron',

        // ── Propel ──
        'Black Cherry'          => 'Cerise Noire',
        'Grape'                 => 'Raisin',
        'Berry'                 => 'Baie',
        // 'Strawberry Lemonade' already defined above — same translation
        'Watermelon'            => 'Pastèque',
        // 'Peach' already defined above

        // ── Muscle Milk ──
        'Vanilla'               => 'Vanille',
        'Chocolate'             => 'Chocolat',
        'Chocolate Peanut Butter' => 'Chocolat au Beurre de Cacahuète',
        'Strawberry'            => 'Fraise',

        // ── Rockstar ──
        'Sugar Free'            => 'Sans Sucre',
        'Whipped Strawberry'    => 'Fraise Fouettée',
        'Orange'                => 'Orange',
        'Recovery Lemonade'     => 'Limonade de Récupération',
        'Recovery Strawberry Lemon' => 'Récupération Fraise-Citron',
        'Recovery Berryade'     => 'Boisson de Récupération aux Baies',
        'Fruit Punch'           => 'Punch aux Fruits',

        // ── Starbucks ──
        'Mocha'                 => 'Moka',
        'Caramel'               => 'Caramel',
        'Tripleshot Caramel'    => 'Triple Shot Caramel',
        'Tripleshot Vanilla'    => 'Triple Shot Vanille',
        'Tripleshot Mocha'      => 'Triple Shot Moka',
        'Pink Drink'            => 'Boisson Rosée',

        // ── 16oz Celsius ──
        'Energy Blueberry Lemonade' => 'Limonade Énergisante à la Myrtille',
        'Energy Tropical Peach'     => 'Pêche Tropicale Énergisante',
        'Energy Watermelon Twist'   => 'Pastèque Énergisante',

        // ── 12oz Celsius ──
        'Mango Lemonade'        => 'Limonade à la Mangue',
        // 'Grape' already defined
        'Cherry Cola'           => 'Cola à la Cerise',
        'Peach Vibe'            => 'Ambiance Pêche',
        'Arctic Vibe'           => 'Ambiance Arctique',
        'Galaxy Vibe'           => 'Ambiance Galaxie',
        'Cosmic'                => 'Cosmique',
        'Blu Razz Lemonade'     => 'Limonade Framboise Bleue',
        // 'Watermelon' already defined
        'Green Apple Cherry'    => 'Pomme Verte à la Cerise',
        'Blue Crush'            => 'Crush Bleu',
        'Dragonberry'           => 'Dragonbaie',

        // ── Gatorade ──
        // 'Orange' already defined
        'Lemon Lime'            => 'Citron-Citron Vert',
        // 'Fruit Punch' already defined
        'Glacier Freeze'        => 'Glacier Glacé',
        'Cool Blue'             => 'Bleu Frais',
        'Rip Tide'              => 'Vague Déferlante',
        // 'Grape' already defined
        'Green Apple'           => 'Pomme Verte',
        // 'Watermelon' already defined

        // ── Alani ──
        'Cherry Limeade'        => 'Citronnade à la Cerise',
        'Classic Cola'          => 'Cola Classique',
        'Strawberry Lemon'      => 'Fraise au Citron',
        'Wild Berry'            => 'Baies Sauvages',
        'Orange Kiss'           => 'Baiser d\'Orange',
        'Sherbet'               => 'Sorbet',
        'Hawaiian Shaved Ice'   => 'Granité Hawaïen',
        'Juicy Peach'           => 'Pêche Juteuse',
        'Cotton Candy'          => 'Barbe à Papa',
        'Breezeberry'           => 'Breezebaie',
        // 'Cosmic' already defined
    ],

    // ── Item word map (fallback for DB items not in item_names) ─
    'item_word_map' => [
        'Cherry'       => 'Cerise',
        'Grape'        => 'Raisin',
        'Strawberry'   => 'Fraise',
        'Berry'        => 'Baie',
        'Limeade'      => 'Citronnade',
        'Lemonade'     => 'Limonade',
        'Peach'        => 'Pêche',
        'Watermelon'   => 'Pastèque',
        'Mango'        => 'Mangue',
        'Apple'        => 'Pomme',
        'Orange'       => 'Orange',
        'Blueberry'    => 'Myrtille',
        'Raspberry'    => 'Framboise',
        'Cranberry'    => 'Canneberge',
        'Black'        => 'Noir',         // base form; gender varies by noun
        'Unsweetened'  => 'Non sucré',
        'Sweet'        => 'Sucré',
        'Extra'        => 'Extra',
        'Zero'         => 'Zéro',
        'Energy'       => 'Énergie',
        'Tropical'     => 'Tropical',
        'Arctic'       => 'Arctique',
        'Galaxy'       => 'Galaxie',
        'Cosmic'       => 'Cosmique',
        'Green'        => 'Vert',
        'Blue'         => 'Bleu',
        'Pink'         => 'Rose',
        'Punch'        => 'Punch',
        'Fruit'        => 'Fruits',
        'Classic'      => 'Classique',
        'Wild'         => 'Sauvage',
        'Cotton'       => 'Barbe à',      // Cotton Candy → Barbe à papa (safety net)
        'Candy'        => 'papa',
        'Juicy'        => 'Juteux',
        'Sherbet'      => 'Sorbet',
        'Ice'          => 'Givré',
        'Chocolate'    => 'Chocolat',
        'Vanilla'      => 'Vanille',
        'Caramel'      => 'Caramel',
        'Mocha'        => 'Moka',
        'Freeze'       => 'Glacé',
        'Cool'         => 'Frais',
        'Diet'         => 'Diet',          // French soda labels use Diet, not Diète
        'Ginger'       => 'Gingembre',
        'Whipped'      => 'Fouetté',
        'Peanut'       => 'Beurre de',    // Peanut Butter → Beurre de cacahuète (safety net)
        'Butter'       => 'cacahuète',
        'Lemon'        => 'Citron',
        'Tea'          => 'Thé',
        'Juice'        => 'Jus',
        'Water'        => 'Eau',
        'Milk'         => 'Lait',
        'Drink'        => 'Boisson',
    ],
];
