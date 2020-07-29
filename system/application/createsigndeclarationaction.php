<?php
@session_start();
require_once( "adminsecurity.php" );

$discovery_id           = $_POST['discovery_id'];
$declaration_text       = $_POST['declaration_text'];
$dec_city               = $_POST['dec_city'];
$dec_state              = $_POST['dec_state'];
$declaration_updated_at = date( "Y-m-d H:i:s" );
$declaration_updated_by = $_SESSION['addressbookid'];

$declaration_updated_at_time = str_replace( ['am','pm'], ['a.m', 'p.m',], date( "g:i a", strtotime( $declaration_updated_at ) ) );
$declaration_updated_at_date = date( "n/j/Y", strtotime( $declaration_updated_at ) );

$declaration_text .= "
    <p>I declare under penalty of perjury under the laws of the State of California that the foregoing is true and correct.
    Executed at {$dec_city}, {$dec_state}. <i>Electronically Signed at ${declaration_updated_at_date}
    ${declaration_updated_at_time}. Pacific Time.</i></p>";
ob_start();
?>
    <table class="tabela1" style="border:none !important">
        <tbody>
        <tr>
            <td colspan="2" align="center"><h3><u>DECLARATION FOR ADDITIONAL DISCOVERY</u></h3></td>
        </tr>
        <tr>
            <td colspan="2" align="justify">
                <?= $declaration_text ?>
            </td>
        </tr>
        </tbody>
    </table>
    <table style="border:none !important" width="100%">
        <tbody>
        <tr>
            <td align="left">
                <br /><br /><br /><br /><br /><br />
                <?= date( 'F j, Y' ) ?>
            </td>
            <td align="right">
                <br /><br /><br /><br /><br /><br />
                By: <?= strtoupper( $_SESSION['name'] ) ?>
                <br />
                Signed electronically,<br><img src="<?= ASSETS_URL ?>images/court.png" style="width: 18px;padding-right: 3px;"> Cal. Rules of Court, rule 2.257
            </td>
        </tr>
        </tbody>
    </table>
<?php
$declaration_text = ob_get_contents();
ob_clean();
$signDeclarationDataArray = [
    "discovery_id"           => $discovery_id, "dec_city" => $dec_city, "dec_state" => $dec_state,
    "declaration_text"       => $declaration_text, "declaration_updated_by" => $declaration_updated_by,
    "declaration_updated_at" => $declaration_updated_at,
];
$_SESSION['signdeclarationdataarray'] = $signDeclarationDataArray;
