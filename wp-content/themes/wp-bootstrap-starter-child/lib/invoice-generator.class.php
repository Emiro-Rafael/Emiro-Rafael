<?php
use setasign\Fpdi\Fpdi;
class InvoiceGenerator extends Fulfillment
{
    private $id;
    private $data;
    private $print;
    private $cart;
    private $generator;
    private $pdf;
    private $item_count;
    private $barcode;
    private $page_count;
    private $billing_data;
    private $shipping_data;
    private $charge_data;
    private $divider_line;
    private $date;
    private $customization_note;

    public function __construct($id, $data, $print, $charge_id, $date, $customization_note)
    {
        parent::__construct();

        $this->id = $id;
        $this->data = $data;
        
        $this->print = $print;

        $this->barcode_path = get_stylesheet_directory() . '/assets/default/barcode.png';

        $this->divider_line = get_stylesheet_directory() . '/assets/default/divider_line.png';
        $stripe = new \Stripe\StripeClient(
            $_ENV['stripe_api_key']
        );
        //$this->charge_data = $stripe->charges->retrieve( $charge_id );

	try {
            $this->charge_data = $stripe->charges->retrieve( $charge_id );
        }
        catch(Exception $e)
        {
            try {
                $invoice = $stripe->invoices->retrieve( $charge_id );
                $this->charge_data = $stripe->charges->retrieve( $invoice->charge );
            }
            catch(Exception $e)
            {
                $this->charge_data = (object)array(
                    'metadata' => (object)array("shipping" => 0),
                    'amount' => 0,
                    'payment_method' => null
                );
            }
            
        }

        $this->date = $date;
        $this->customization_note = $customization_note;
    }

    private function setAddressData()
    {
        $this->billing_data = array_combine( 
            array_keys($this->data), 
            array_map(
                function($value)
                {
                    return iconv('utf-8', 'cp1252', htmlspecialchars_decode($value));
                },
                $this->data
            )
        );
        $this->billing_data['country'] = $this->billing_data['country'] ?? 'US';
        
        $this->shipping_data = array_combine( 
            array_keys($this->data), 
            array_map(
                function($value)
                {
                    return iconv('utf-8', 'cp1252', htmlspecialchars_decode($value));
                },
                $this->data
            )
        );
        
        $this->shipping_data['country'] = $this->shipping_data['country'] ?? 'US';
    }

    private function pdfSetter()
    {
        $this->pdf->setSourceFile( get_stylesheet_directory() . '/assets/default/invoice.pdf');
        $this->pdf->SetMargins(0,0,0);
    }

    private function setPageCount()
    {
        $this->page_count = floor( $this->items_count / 13 );
        
        $remainder = $this->items_count % 13;
        
        if($remainder != 0)
        {
            $this->page_count++;
        }
    }

    private function generateDefault()
    {

    }

    private function generateForPrint()
    {
        $shipping_total = $this->charge_data->metadata->{'shipping'};
        $total = number_format( ($this->charge_data->amount / 100), 2 );
        $subtotal = $total - $shipping_total;

        for( $page_number = 1; $page_number <= $this->page_count; $page_number++)
        {
            $this->pdf->AddPage();
            $tplIdx = $this->pdf->importPage(1);
            $this->pdf->useTemplate($tplIdx);
            $this->pdf->SetFont('Helvetica', '' , 11);
            $this->pdf->SetTextColor(0, 0, 0);
            
            $x = 162;
            $y = 29 - ($page_number / 2);
            $this->pdf->SetXY($x, $y);
            $this->pdf->Write($page_number, date('m-d-Y h:iA', strtotime($this->date)));
            
            $x = 162;
            $y = 35 - ($page_number / 2);
            $this->pdf->SetXY($x, $y);
            $this->pdf->Write($page_number, '#'. $this->id);
            
            $x = 12;
            $y = 56 - ($page_number / 2);
            $this->pdf->SetXY($x, $y);
            $this->pdf->Write($page_number, strtoupper($this->billing_data['shipping_name']));

            $this->pdf->SetXY($x,  $y += 5);
            $this->pdf->Write($page_number, strtoupper($this->billing_data['address_1']));

            if($this->billing_data['address_2'] != '')
            {
                $this->pdf->SetXY($x,  $y += 5);
                $this->pdf->Write($page_number, strtoupper($this->billing_data['address_2']));
            }

            $this->pdf->SetXY($x,  $y += 5);
            $this->pdf->Write($page_number, strtoupper(trim($this->billing_data['city']).', '.trim($this->billing_data['state'])).' '.$this->billing_data['zipcode']);
            
            if($this->billing_data['country'] != 'US')
            {
                $this->pdf->SetXY($x,  $y += 5);
                $this->pdf->Write($page_number, strtoupper($this->billing_data['country']));
            }
            
            $x = 129;
            $y = 56 - ($page_number / 2);
            $this->pdf->SetXY($x, $y);
            $this->pdf->Write($page_number, strtoupper($this->shipping_data['shipping_name']));
            $this->pdf->SetXY($x,  $y += 5);
            $this->pdf->Write($page_number, strtoupper($this->shipping_data['address_1']));

            if($this->shipping_data['address_2'] != '')
            {
                $this->pdf->SetXY($x,  $y += 5);
                $this->pdf->Write($page_number, strtoupper($this->shipping_data['address_2']));
            }

            $this->pdf->SetXY($x,  $y += 5);
            $this->pdf->Write($page_number, strtoupper(trim($this->shipping_data['city']).', '.trim($this->shipping_data['state'])).' '.$this->shipping_data['zipcode']);
            
            if($this->shipping_data['country'] != 'US')
            {
                $this->pdf->SetXY($x,  $y += 5);
                $this->pdf->Write($page_number, strtoupper($this->shipping_data['country']));
            }
            
            /*
            foreach($order_data['shipping_lines'] as $shipping_line)
            {
                $shipping_title = $shipping_line->get_method_title();
            }
            */

            if($shipping_total == 0)
            {
                $subtotal = '$' . number_format( (float)$total, 2, '.', '' );
            }
            else
            {
                $subtotal = '$' . number_format( ($total - $shipping_total), 2, '.', '' );
            }
            
            
            $this->pdf->SetXY(141, 231 - ($page_number / 2));
            $this->pdf->Write($page_number, $subtotal);

            if($shipping_total == 0)
            {
                $shipping = 'Free';
            }
            else
            {
                $shipping = '$'.$shipping_total;
            }
            
            $this->pdf->SetXY( 141, 239 - ($page_number / 2) );
            $this->pdf->Write( $page_number, $shipping );
            
            $this->pdf->SetXY( 141, 247 - ($page_number / 2) );
            $this->pdf->Write( $page_number, '$' . number_format( (float)$total, 2, '.', '' ) );
            
            $this->pdf->SetXY( 141, 255 - ($page_number / 2) );

            $payment_method = ( substr($this->charge_data->payment_method, 0 ,4) == 'card' ) ? "Stripe Accepted Cards" : "";
            $this->pdf->Write( $page_number, ucwords( $payment_method ) );
                
            $this->pdf->Image( $this->barcode_path, 5, 250, 65, 15 );
            
            
            
            $x = 19;
            $y = 98.5 - (($page_number - 1) * 6.5);
            $x2 = 13;
            $y2 = 94;
            
            $j = 1;
            $range = range( 
                1 + ( 13 * ( $page_number - 1 ) ), 
                13 + ( 13 * ( $page_number - 1 ) ) 
            );

            foreach ($this->items as $post_id => $details)
            { 
                if( !in_array($j, $range) )
                {
                    $j++;
                    continue;
                }

                switch( get_post_type($post_id) )
                {
                    case 'snack':
                        $snack = new SnackModel($post_id);
                        $description = get_the_title( $post_id );
                        if(is_array($details)) {
                            foreach($details as $quantity)
                            {
                                $quantity = $quantity;
                            }
                        } else {
                            $quantity = $details;
                        }
                        $this->_generateLineItem( $x, $y, $post_id, $description, $quantity, $j );
                        $j++;

                        $this->pdf->Image($this->divider_line, $x2, $y2 += 9, 190, 0);
                        $x = 19;
                        $y += 8.5;
                        break;

                    case 'country':
                    case 'collection':
                        if(!is_array($details)) {
                            $description = get_the_title( $post_id );
                            $quantity = $details;
                            $this->_generateLineItem( $x, $y, $post_id, $description, $quantity, $j );
                        } else {
                            foreach( $details as $crate_size => $quantity)
                            {
                                $description = get_the_title( $post_id ) . ' ' . CountryModel::$pretty_names[$crate_size];
                                $this->_generateLineItem( $x, $y, $post_id, $description, $quantity, $j, $crate_size );
                            }
                        }

                        $j++;

                        $this->pdf->Image($this->divider_line, $x2, $y2+=9, 208, 0);
                        $x = 19;
                        $y += 8.5;

                        break;
                }
            }
            
            $this->pdf->SetFont('Helvetica', '' , 9);
            if(!empty($this->customization_note)){
                $this->pdf->SetXY( 20,  $y + 8 + (($page_number - 1) * 3.5) );
                $this->pdf->Write( $page_number,  "CUSTOMIZATION NOTES: ". strtoupper($this->customization_note) );

                $offsetY = 15;
            }
            
            else {
                $offsetY = 8;
            }
            $this->pdf->SetXY(20,  $y + $offsetY + (($page_number - 1) * 3.5));
            $this->pdf->Write($page_number, "PLEASE NOTE: EXPIRATION DATES FOR THESE ITEMS MAY VARY BETWEEN DD/MM/YYYY AND MM/DD/YYYY.");
            $this->pdf->SetXY(20,  $y + $offsetY + 5 + (($page_number - 1) * 3.5));
            $this->pdf->Write($page_number, "CUSTOMIZATION NOTES ONLY APPLY TO YOUR MONTHLY SUBSCRIPTION. SNACKSHOP AND ADDONS ARE SHIPPED");
            $this->pdf->SetXY(20,  $y + $offsetY + 10 + (($page_number - 1) * 3.5));
            $this->pdf->Write($page_number, "AS ORDERED.");
            $this->pdf->SetFont('Helvetica', '' , 11);
            
        }
    }

    private static function convertItemName($name)
    {
        $targets = array('&#8217;', '&#8211;', '&#281;', '&#322;');
        $results = array("'", "-", "e", "l");
        $name = iconv('utf-8', 'cp1252', htmlspecialchars_decode(str_replace($targets, $results, $name)));
        $name = strlen($name) > 40 ? substr($name,0,40)."..." : $name;

        return $name;
    }

    private function _generateLineItem($x, $y, $post_id, $description, $quantity, $page_number, $crate_size = null)
    {
        $dbh = SCModel::getSnackCrateDB();
        if( empty($crate_size) )
        {
            $stmt = $dbh->prepare("SELECT single_item_price FROM candybar_order_item WHERE order_id = :order_id AND item_id = :post_id");
            $stmt->bindParam(":order_id", $this->id);
            $stmt->bindParam(":post_id", $post_id);
            $stmt->execute();
            
            $item_price = $stmt->fetch(PDO::FETCH_COLUMN);

            $stmt = null;
        }
        else
        {
            $stmt = $dbh->prepare("SELECT single_item_price FROM candybar_order_item WHERE order_id = :order_id AND item_id = :post_id AND `name` LIKE '%{$crate_size}'");
            $stmt->bindParam(":order_id", $this->id);
            $stmt->bindParam(":post_id", $post_id);
            $stmt->execute();
            
            $item_price = $stmt->fetch(PDO::FETCH_COLUMN);

            $stmt = null;
        }

        $item_total = number_format( $item_price * $quantity, 2 );

        $name = self::convertItemName($description);
        
        $this->pdf->SetXY($x, $y);
        $this->pdf->Write($page_number, $post_id);
        $this->pdf->SetXY(47, $y);
        $this->pdf->Write($page_number, stripslashes($name));
        $this->pdf->SetXY(127, $y);
        $this->pdf->Write($page_number, $quantity);
        $this->pdf->SetXY(154, $y);
        $this->pdf->Write($page_number, '$'.number_format((float)$item_price, 2, '.', ''));
        $this->pdf->SetXY(185, $y);
        $this->pdf->Write($page_number, '$'.number_format((float)$item_total, 2, '.', ''));
    }

    private function setItems()
    {
        $stmt = $this->dbh->prepare("SELECT purchased FROM " . self::$order_table . " WHERE id = :id");
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        
        $serialized_items = $stmt->fetch(PDO::FETCH_COLUMN);
        $purchased = str_replace("'", '"', $serialized_items);
        $this->items = unserialize($purchased);
    }

    public function generate()
    {
        $this->generator = new Picqer\Barcode\BarcodeGeneratorPNG();
        $barcode = $this->generator->getBarcode($this->id, $this->generator::TYPE_CODE_128,1, 20);
        file_put_contents( $this->barcode_path, $barcode);

        $this->pdf = new Fpdi();
        $this->pdf->setSourceFile( get_stylesheet_directory() . '/assets/default/invoice.pdf');
        $this->pdf->SetMargins(0,0,0);

        
        $this->setAddressData();

        $this->setItems();
        $this->items_count = count($this->items);
        $this->setPageCount();
        if( $this->print )
        {
            $this->generateForPrint();
        }
        else
        {
            $this->generateDefault();
        }
        //$this->pdfSetter();
        $this->pdf->Output( get_stylesheet_directory() . '/assets/generated_files/candybar_order_'.$this->id.'.pdf', 'F' );
    }
}
