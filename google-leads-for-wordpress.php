<?php
/** 
 * Plugin Name: Google lead integration
 * Plugin URI: https://oliverimarco.it
 * Description: Plugin per integrare l'estensione lead di Google ADS
 * Version: 1.0
 * Requires PHP: 7.4
 * Author: Marco Oliveri
 * Author URI: https://oliverimarco.it/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: oliverimarco.it
*/


//Inizializzazione tabella
function insert_credentials($data_t) {
  $servername = DB_HOST;
   $username = DB_USER;
   $password = DB_PASSWORD;
   $dbname = DB_NAME;
   $conn = new mysqli($servername, $username, $password, $dbname);
   if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
  }
   $sql = "UPDATE credentials SET google_key='$data_t' WHERE id='main_key'";
   $conn->query($sql);
   $conn->close();
}

function pluginprefix_activate() {
    global $wpdb;
    $table_name = "lead_ads";
    $table_name2 = "credentials";
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (id int(20) AUTO_INCREMENT,
    PRIMARY KEY id (id),
     nome varchar(40),
     cognome varchar(40),
     email varchar(40),
     tel varchar(40),
     azienda varchar(40)
     ) $charset_collate";
     $sql2 = "CREATE TABLE IF NOT EXISTS $table_name2 (id varchar(20),
     UNIQUE id (id),
     google_key varchar(40)) $charset_collate";
$sql3 = "INSERT INTO credentials (id)
SELECT * FROM (SELECT 'main_key' AS id) AS temp
WHERE NOT EXISTS (
    SELECT id FROM credentials WHERE id = 'main_key'
) LIMIT 1;";
     require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
     dbDelta($sql);
     dbDelta($sql2);
     dbDelta($sql3);
}
register_activation_hook( __FILE__, 'pluginprefix_activate');

//Prendo dati da db
function get_data() {
    global $wpdb;
    return $wpdb->get_results("SELECT * FROM lead_ads");
}
function get_data_2() {
  global $wpdb;
  return $wpdb->get_results("SELECT google_key FROM credentials WHERE id='main_key'");
}

add_action('admin_menu', 'plugin_page');
function plugin_page() {
    add_menu_page(
    __('Google Leads', 'textdomain'),
    __('Google Leads', 'textdomain'),
    'manage_options',
    'google-form',
    'page_block',
    'dashicons-admin-page',
    6
);
}

function page_block() {
    $source = plugin_dir_url( __FILE__ ) . 'connect.php';
    $path = plugin_dir_url( __FILE__ );
    $data2 = get_data_2();
    ?>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo $path; ?>assets/css/custom.css">
    <link rel="stylesheet" href="<?php echo $path; ?>bootstrap/css/bootstrap.min.css">

</head>
<body>
    <div class="main">
    <div class="container">
      <div class="row mt-3">
        <div class="col">
    <h1>Google leads</h1>
</div>
</div>
    <div class="row">
    <div class="col">
    <h3>Guida configurazione</h3>
    </div>
</div>
      <div class="row">
        <div class="col">
    <div class="accordion accordion-section" id="accordionExample">
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingOne">
      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
        Link Configurazione Google
      </button>
    </h2>
    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
      <div class="accordion-body">
      <strong>Inserisci il seguente url su Google:</strong> <?php echo $source;   ?>
      </div>
</div>
<div class="accordion-item">
    <h2 class="accordion-header" id="headingTwo">
      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
        Chiave Google
      </button>
    </h2>
    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
      <div class="accordion-body">
      <div class="container"><div class="row"><div class="col">La key che dovrai inserire su Google: <form method="POST"><input type="text" name="chiave" class="form-control" value="<?php echo $data2[0]->google_key;  ?>"> </div><div class="col custom-col"><input type="submit" value="Invia" name="submit" class="btn btn-primary"></form></div></div></div>
      </div>
      <?php

if($_SERVER["REQUEST_METHOD"] == "POST") {
  $var_post = $_POST['chiave'];
  insert_credentials($var_post);
    wp_reset_postdata();
    header("Refresh:0");
  }

?>
</div>
    </div>
    </div>
    </div>
  </div>
  <div class="row mt-3">
    <div class="col">
    <h3>Tabella dati</h3>
    </div>
</div>
<div class="row">
  <div class="col">
<table class="table table-striped mt-1">
    <thead>
  <tr>
      <th>ID</th>
      <th>Nome</th>
      <th>Cognome</th>
      <th>Email</th>
      <th>Telefono</th>
      <th>Azienda</th>
  </tr>
  </thead>
  <tbody>
  <?php
$queries = get_data();
foreach($queries as $query) {
    echo "<tr><td>". $query->id . "</td><td>" . $query->nome . "</td><td>" . $query->cognome . "</td><td>" . $query->email . "</td><td>" . $query->tel . "</td><td>" . $query->azienda . "</td></tr>";
} 
  ?>
  </tbody>
  </table>
  <button id="download-button" class="btn btn-outline-primary">
  <span class="fas fa-download mr-2"></span>
  Scarica</button>
  </div>
</div>
</div>
  <script type="text/javascript">

	function downloadCSVFile(csv, filename) {
	    var csv_file, download_link;

	    csv_file = new Blob([csv], {type: "text/csv"});

	    download_link = document.createElement("a");

	    download_link.download = filename;

	    download_link.href = window.URL.createObjectURL(csv_file);

	    download_link.style.display = "none";

	    document.body.appendChild(download_link);

	    download_link.click();
	}

		document.getElementById("download-button").addEventListener("click", function () {
		    var html = document.querySelector("table").outerHTML;
			htmlToCSV(html, "google-data.csv");
		});


		function htmlToCSV(html, filename) {
			var data = [];
			var rows = document.querySelectorAll("table tr");
					
			for (var i = 0; i < rows.length; i++) {
				var row = [], cols = rows[i].querySelectorAll("td, th");
						
				 for (var j = 0; j < cols.length; j++) {
				        row.push(cols[j].innerText);
		                 }
				        
				data.push(row.join(","));		
			}

			//to remove table heading
			//data.shift()

			downloadCSVFile(data.join("\n"), filename);
		}

	</script>
<script src="<?php echo $path; ?>bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
<?php
}