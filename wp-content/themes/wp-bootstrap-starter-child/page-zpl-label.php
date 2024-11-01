<?php

/**
 * The template for displaying Create Account
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 * 
 * Template Name: ZPL-LABEL
 */
get_header();


// ZPL Label Function 
function generateLabelZPL($elements)
{
    $zpl_labels = [];
    $max_lines_per_label = 11; // Adjust based on space availability for contents
    $contents_chunks = array_chunk($elements['contents'], $max_lines_per_label);
    $total_pages = count($contents_chunks);
    $current_page = 1;

    $logo_data = "^FO30,20^GFA,2750,2750,25,J01gY01,J03hF8,J07hFC,J0hGFE,I01hHF,I03hHF8,I07hHFC,I0hIFE,001hJF,003hJF8,007hJFC,00hKFE,01hLF,03hLF8,07hLFC,0hMFE,1hNF,3hNF87hNFC7hNFE7hNFC:7hNFE7hNFC::::7KFE001IF01IF00JF007KF003IF01FFE00JFC7KF8I07FF00IF00IFE007JF8I07FE01FFC01JFC7KFJ01FF007FF00IFE003JFJ03FE01FF803JFC7JFEK0FF007FF00IFE003IFCK0FE01FF003JFC7JFCK07F003FF00IFC003IF8K0FE01FE007JFC7JF8K03F001FF00IFC001IFL07E01FC00KFC7JF8K03FI0FF00IF8001IFL03E01F801KFC7JF007F807FI07F00IF8I0FFE001E003E01F803KFC7JF00FFE1FFI07F00IF8I0FFE007F807E01F007KFC7JF00IF7FFI03F00IFJ07FC00FFE1FE01E007KFC7JF007KFI01F00IF00807FC01FFE7FE01C00LFC7JFI07JFJ0F00FFE01807FC03KFE01801LFC7JFJ01IFJ0F00FFE01C03FC03KFE01803LFC7JF8J03FFJ0700FFC01C03F803KFEJ03LFC7JF8K0FFJ0300FFC03C01F803KFEJ03LFC7JFCK07F0080100FFC03E01F803KFEJ01LFC7JFEK03F00CJ0FF803E01F803KFEK0LFC7KFCJ01F00EJ0FF807F00F803KFEK0LFC7LFCI01F00FJ0FF007F00FC03KFEK07KFC7NF001F00FJ0FFL07C03KFEK03KFC7NFE01F00F8I0FFL07C01FFE3FE001803KFC7JFC7FFE01F00FCI0FEL03C00FFC0FE003C01KFC7JFC3FFE01F00FEI0FEL03E007F807E007C01KFC7JF803F801F00FEI0FCL03EL03E007E00KFC7JF8K01F00FFI0FCL01FL03E00FF007JFC7JFL03F00FF800F8L01F8K07E01FF007JFC7JFL03F00FFC00F803FFE00FCK07E01FF803JFC7JFL07F00FFE00F807FFE00FEJ01FE01FFC01JFC7JFCK0FF00FFE00F007IF00FFJ03FE01FFC01JFC7KFJ03FF00IF00F007IF007FCI0FFE01FFE00JFC7KFE001QF7MF7FF003OFEJFC7hNFC::::::7MF03gYFC7LF8003FFEK0LF801FF8L03CL0JFC7KFCJ0FFEK03KF001FF8L03CL0JFC7KF8J03FEL0KFI0FF8L03CL0JFC7KFK01FEL07JFI0FF8L03CL0JFC7JFEL0FEL03IFEI0FF8L03CL0JFC7JFCL0FEL03IFEI07F8L03CL0JFC7JF8L07EL01IFCI07F8L03CL0JFC7JF80038003E008I01IFCI03F8L03CL0JFC7JF001FF00FE01FFC01IFCI03IFE00IFC01NFC7JF003FF83FE01FFC01IF8I03IFE00IFC01NFC7JF007FFC7FE01FFE00IF80401IFE00IFC01NFC7JF00IFDFFE01FFE00IF00601IFE00IFC01NFC7IFE00LFE01FFE01IF00600IFE00IFCJ03KFC7IFE00LFE01FFC01FFE00E00IFE00IFCJ03KFC7IFE01LFE00FF801FFE00F007FFE00IFCJ03KFC7IFE01LFEL01FFE01F007FFE00IFCJ03KFC7IFE01LFEL03FFC01F807FFE00IFCJ03KFC7IFE01LFEL03FFC01F803FFE00IFCJ03KFC7IFE00LFEL07FF803F803FFE00IFCJ03KFC7IFE00LFEL0IF803FC01FFE00IFCJ03KFC7IFE00LFEK03IF8K01FFE00IFC01NFC7JF007FFC7FEK03IFL01FFE00IFC01NFC7JF003FF81FE01FC01IFM0FFE00IFC01NFC7JF001FF00FE01FE00FFEM0FFE00IFC01NFC7JF8007C003E01FE00FFEM07FE00IFCL0JFC7JF8L03E01FF007FCM07FE00IFCL0JFC7JFCL07E01FF003FCM03FE00IFCL0JFC7JFEL0FE01FF803FC01IF003FE00IFCL0JFC7KFK01FE01FFC01F801IF803FE00IFCL0JFC7KF8J03FE01FFC00F803IF801FE00IFCL0JFC7KFCJ07FE01FFE00F003IFC01FE00IFCL0JFC7LFI01FFE01IF007007IFC00FE00IFCL0JFC7LFE00gYFC7hNFC::::7hNFE7hNFC7hNFE7hNFC,^FS"; // Placeholder for static logo ZPL code.

    $logo_width = 100; // Change as needed
    $logo_height = 50; // Change as needed
    // Calculate the total bytes based on the width and height
    $total_bytes = ($logo_width * $logo_height + 7) / 8; // Total bytes for a bitmap image
    // Format the logo ZPL
    $logo_zpl = "^FO10,20^GFA,$total_bytes,$logo_height,$logo_width,$logo_data";

    foreach ($contents_chunks as $contents_chunk) {
        $zpl = "^XA"; // Start of ZPL
        if ($current_page == 1) {
            // Static Logo
            $zpl .= $logo_zpl;
            // Assembly Line Designation
            $zpl .= "^FO470,20^GB320,100,5^FS"; // Draws a box (border) with width 400, height 100, and thickness 3
            $zpl .= "^FO500,35^A0N,90,90^FD" . $elements['assemblyLine'] . "^FS";

            // Data Matrix
            $zpl .= "^FO610,160^BXN,10,200^FD" . $elements['dataMatrix'] . "^FS";

            // Shipping Address
            $zpl .= "^FO30,170^A0N,30,30^FDSHIP TO:^FS";
            $y_position = 220;
            foreach ($elements['address'] as $line) {
                $zpl .= "^FO30,$y_position^A0N,24,24^FD" . $line . "^FS";
                $y_position += 25; // Move down for the next line
            }

            $zpl .= "^FO20,400^GB770,5,5^FS";
        }

        // Contents List
        if ($current_page == 1) {
            $zpl .= "^FO30,440^A0N,25,25^FDCONTENTS:^FS";
            $y_position = 490;
        } else {
            $zpl .= "^FO30,50^A0N,25,25^FDCONTENTS(cont):^FS";
            $y_position = 100;
        }

        foreach ($contents_chunk as $content) {
            $zpl .= "^FO30,$y_position^A0N,20,26^FD" . $content . "^FS";
            $y_position += 35;
        }

        // Page Header for Multi-page Labels
        if ($total_pages > 1) {
            $zpl .= "^FO20,1190^A0N,15,15^FDPage $current_page of $total_pages^FS";
        }
        // Custom Order Note on Last Page Only
        if ($current_page == $total_pages) {
            $y_position += 10;
            $label_width = 812; // Label width in dots (for a 4-inch label at 203 DPI)
            $text_length = strlen($line); // Number of characters in the line
            $character_width = 14; // Approximate width of each character at font size 28
            $text_width = $text_length * $character_width;
            // Calculate the x-coordinate for centered text
            $x_position = ($label_width - $text_width) / 2;
            // Construct the ZPL command with centered x-position
            // $y_position = 700;
            $y_position += 10;
            if (isset($elements['customOrderNote']) && !empty($elements['customOrderNote'])) {
                $zpl .= "^FO272,$y_position^A0N,28,28^FD*** CUSTOM ORDER ***^FS";
            }

            $y_position += 35;
            // foreach (explode("\n", $elements['customOrderNote']) as $line) {
            // Check if 'customOrderNote' is set and not empty
            if (isset($elements['customOrderNote']) && !empty($elements['customOrderNote'])) {

                $margin = 10; // Define the left and right margin

                foreach ($elements['customOrderNote'] as $line) {
                    $words = explode(' ', $line); // Split the line into words
                    $current_line = '';

                    foreach ($words as $word) {
                        $current_line_with_word = (empty($current_line) ? '' : $current_line . ' ') . $word;
                        $text_width = strlen($current_line_with_word) * $character_width;

                        if ($text_width > ($label_width - 2 * $margin)) {
                            // Print the current line if adding the next word exceeds label width (considering margins)
                            $x_position = $margin + (($label_width - 2 * $margin - (strlen($current_line) * $character_width)) / 2);
                            $zpl .= "^FO$x_position,$y_position^A0N,28,28^FD" . $current_line . "^FS";
                            $y_position += 30;

                            // Start a new line with the current word
                            $current_line = $word;
                        } else {
                            $current_line = $current_line_with_word;
                        }
                    }

                    // Print the remaining words in the line if any
                    if (!empty($current_line)) {
                        $x_position = $margin + (($label_width - 2 * $margin - (strlen($current_line) * $character_width)) / 2);
                        $zpl .= "^FO$x_position,$y_position^A0N,28,28^FD" . $current_line . "^FS";
                        $y_position += 25;
                    }
                }
            }

            $zpl .= "^FO20," . $y_position + 12 . "^GB770,5,5^FS";


            $y_position += 40;

            // Assuming $label_width is defined correctly, e.g., 812 for 4-inch width at 203 DPI
            $label_width = 310;

            // Parameters for PDF417 barcode
            $barcode_element_width = 7.5; // Adjust this to desired element width
            $barcode_columns = 4; // Can be adjusted depending on data size and needs
            $barcode_width = $barcode_columns * $barcode_element_width; // Estimate width

            // Calculate X position for centering
            $x_position = ($label_width - $barcode_width) / 2;
            $x_position = 90;
            // Construct ZPL for PDF417 barcode
            $zpl .= "^FO$x_position,1015^B7N,$barcode_columns,$barcode_element_width^FD" . $elements['barcodeData'] . "^FS";

            $width = 700; // Set the width you want for the text block
            $zpl .= "^FO50,1180^FB{$width},1,0,C,0^A0N,28,28^FD" . $elements['additionalText'] . "^FS";
        }

        $zpl .= "^XZ"; // End of ZPL

        $zpl_labels[] = $zpl;
        $current_page++;
    }
    return implode("\n", $zpl_labels);
}

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $address_input = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $address_input = str_replace("\\n", "\n", $address_input);
    $address_input = trim($address_input);
    $address_input = mb_convert_encoding($address_input, 'UTF-8', 'auto');
    $address_lines = explode("\n", $address_input);

    $elements = [
        'assemblyLine' => $_POST['assemblyLine'],
        'dataMatrix' => $_POST['dataMatrix'],
        'address' => $address_lines,
        'contents' => explode("\n", trim($_POST['contents'])),
        'barcodeData' => $_POST['barcodeData'],
        'additionalText' => $_POST['additionalText']
    ];

    if (!empty($_POST['customOrderNote'])) {
        $elements['customOrderNote'] = explode("\n", $_POST['customOrderNote']);
    }

    $zpl_string = generateLabelZPL($elements);

    // Save ZPL data to file
    // $zpl_filename = '/label_output.zpl';
    // file_put_contents($zpl_filename, $zpl_string);


    // File path for saving ZPL
    $uploads_dir = wp_upload_dir();
    $zpl_filename = 'label_output.zpl';
    $file_path = $uploads_dir['path'] . '/' . $zpl_filename;
    file_put_contents($file_path, $zpl_string);

    // Generate previews for each complete ZPL label block
    $previews = [];
    $zpl_labels = preg_split('/(?=\^XA)/', $zpl_string); // Split keeping the ^XA at the start of each label
    foreach ($zpl_labels as $zpl) {
        $zpl = trim($zpl); // Clean up whitespace
        if (empty($zpl)) continue;

        // Ensure each ZPL block has a start (^XA) and end (^XZ)
        if (strpos($zpl, '^XA') !== 0) {
            $zpl = '^XA' . $zpl;
        }
        if (!str_ends_with($zpl, '^XZ')) {
            $zpl .= '^XZ';
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://api.labelary.com/v1/printers/8dpmm/labels/4x6/0/');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $zpl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: image/png"));

        $result = curl_exec($ch);
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200) {
            $previews[] = 'data:image/png;base64,' . base64_encode($result);
        } else {
            $previews[] = "Error generating preview.";
        }
        curl_close($ch);
    }

    // Provide download link
    $file_url = $uploads_dir['url'] . '/' . $zpl_filename;
    // echo
    // "<script>
    //     document.addEventListener('DOMContentLoaded', function() {
    //         var a = document.createElement('a');
    //         a.href = '{$file_url}';
    //         a.download = '{$zpl_filename}';
    //         a.style.display = 'none';
    //         document.body.appendChild(a);
    //         a.click();
    //         document.body.removeChild(a);
    //     });
    // </script>";
    // echo "<p>Your ZPL file is ready: <a href='{$file_url}' download>Download Label</a></p>";

    require_once get_stylesheet_directory() . '/lib/zebra-print.class.php';
    $zebraPrint = new ZebraPrint();
    $printerId = sanitize_text_field($_POST['printerId']); // Example input for printer ID
    $fileUrl = $file_url;

    // Assuming your ZebraPrint class has a method like this
    $result = $zebraPrint->sendFileToPrinter($printerId, $fileUrl);

    if ($result) {
        echo "Label sent to the printer successfully.";
    } else {
        echo "Failed to send label to the printer.";
    }
}


?>
<section id="primary" class="content-area mb-5">
    <div class="create-account py-3 py-md-4">
        <div class="container">
            <a class="h6 h5-lg h4-xl font-weight-semibold text-gray-dark d-flex align-items-center" href="<?php echo get_bloginfo('url') ?>">
                <span class="text-primary mb-0 mr-1 mr-lg-2"><i class="fas fa-arrow-circle-left"></i></span>
                Back to CandyBar
            </a>
            <div class="row">
                <div class="col">
                    <h1>Label Generator</h1>
                    <form method="POST">
                        <div class="mb-3">
                            <label for="assemblyLine" class="form-label">Assembly Line</label>
                            <input type="text" class="form-control" id="assemblyLine" name="assemblyLine" required>
                        </div>
                        <div class="mb-3">
                            <label for="dataMatrix" class="form-label">Data Matrix</label>
                            <input type="text" class="form-control" id="dataMatrix" name="dataMatrix" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Shipping Address</label>
                            <input class="form-control" id="address" name="address" required>
                        </div>
                        <div class="mb-3">
                            <label for="contents" class="form-label">Contents (One per line)</label>
                            <textarea class="form-control" id="contents" name="contents" rows="5" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="customOrderNote" class="form-label">Custom Order Note</label>
                            <input class="form-control" id="customOrderNote" name="customOrderNote">
                        </div>
                        <div class="mb-3">
                            <label for="barcodeData" class="form-label">Barcode Data</label>
                            <input type="text" class="form-control" id="barcodeData" name="barcodeData" required>
                        </div>
                        <div class="mb-3">
                            <label for="additionalText" class="form-label">Additional Text</label>
                            <input type="text" class="form-control" id="additionalText" name="additionalText">
                        </div>
                        <div class="mb-3">
                            <label for="printerId" class="form-label">Printer Id</label>
                            <input type="text" class="form-control" id="printerId" name="printerId">
                        </div>
                        <button type="submit" class="btn btn-primary">Generate Label</button>
                    </form>
                </div>

                <div class="col">

                    <!-- Button for downloading ZPL -->
                    <?php
                    if ($file_url) { ?>
                        <div class="mt-3">
                            <a href="<?= $file_url ?>" class="btn btn-success mt-3" download>Download ZPL</a>
                        </div>
                    <?php } ?>



                    <?php if (isset($previews)): ?>
                        <h2 class="mt-5">Label Previews:</h2>
                        <?php foreach ($previews as $preview): ?>
                            <?php if (strpos($preview, 'data:image') !== false): ?>
                                <img src="<?= $preview ?>" class="img-fluid border mb-3" alt="Label Preview">
                            <?php else: ?>
                                <p><?= $preview ?></p>
                            <?php endif; ?>
                        <?php endforeach; ?>


                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</section>