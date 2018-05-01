<?php
require('../../fpdf/fpdf.php');
include("../../config.php");

$userid = $_GET["userid"];
$completedcourses = $_GET["txt"];

$dbQuery=$db->prepare("select fullname from users where id=:id");
$dbParams=array('id'=>$userid);
$dbQuery->execute($dbParams);
$dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC);
$fullname=$dbRow["fullname"];


class PDF extends FPDF
{
	function Header()
	{
		global $title;

		// Arial bold 15
		$this->SetFont('Arial','B',15);
		// Calculate width of title and position
		$w = $this->GetStringWidth($title)+6;
		$this->SetX((210-$w)/2);
		// Colors of frame, background and text
		$this->SetDrawColor(30,136,255);
		$this->SetFillColor(30,136,255);
		$this->SetTextColor(255,255,255);
		// Thickness of frame (1 mm)
		$this->SetLineWidth(1);
		// Title
		$this->Cell($w,9,$title,1,1,'C',true);
		// Line break
		$this->Ln(10);
	}

	function Footer()
	{
		// Position at 1.5 cm from bottom
		$this->SetY(-15);
		// Arial italic 8
		$this->SetFont('Arial','I',8);
		// Text color in gray
		$this->SetTextColor(128);
		// Page number
		$this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
	}

	function ChapterTitle($num, $label)
	{
		// Arial 12
		$this->SetFont('Arial','',12);
		// Background color
		$this->SetFillColor(200,220,255);
		// Title
		$this->Cell(0,6,$label,0,1,'L',true);
		// Line break
		$this->Ln(4);
	}

	function ChapterBody($file)
	{
		// Read text file
		$txt = file_get_contents($file);
		// Times 12
		$this->SetFont('Arial','',12);
		// Output justified text
		$this->MultiCell(0,5,$txt);
		// Line break
		$this->Ln();
		// Mention in italics
		$this->SetFont('','I');
		$this->Cell(0,5,'(end of record)');
	}

	function PrintChapter($num, $title, $file)
	{
		$this->AddPage();
		$this->ChapterTitle($num,$title);
		$this->ChapterBody($file);
	}
}

$pdf = new PDF();
$title = $sitename . " | " . $fullname . "'s Learning Record";
$pdf->SetTitle($title);
$pdf->SetAuthor($sitename);
$pdf->PrintChapter(1,'COMPLETED COURSES',$completedcourses);
$pdf->Output();
?>
?>