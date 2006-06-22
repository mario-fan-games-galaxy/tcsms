<?php
// Graph Lib

//===============================================================================
// Pie Chart class accepts serialized array argument and generates a pie chart
//===============================================================================
class piechart
{
	var $center_x         = 0;
	var $center_y         = 0;
	var $circle_width     = 0;
	var $circle_height    = 0;
	var $image_width      = 0;
	var $image_height     = 0;
	var $colors           = array();
	var $colors_data      = array();
	var $split_chart      = false;
	var $segment_data     = array();
	
	var $image            = 0;
	var $last_arc         = 0;
	
	function init($serial_data)
	{
		$data = unserialize($serial_data);

		$this->center_x = $data['center_x'];
		$this->center_y = $data['center_y'];
		$this->circle_width = $data['circle_width'];
		$this->circle_height = $data['circle_height'];
		$this->image_width = $data['image_width'];
		$this->image_height = $data['image_height'];
		$this->colors_data = $data['colors_data'];
		$this->split_chart = $data['split_chart'];
		$this->segment_data = $data['segment_data'];
	}
	
	function Build()
	{
		// Create initial workspace
		$this->image = ImageCreateTrueColor($this->image_width, $this->image_height);
		ImageAntiAlias($this->image, true);
		
		// Generate Colors
		for ($x=0; $x<sizeof($this->colors_data); $x++)
		{
			$this->colors[$this->colors_data[$x][name]] = $this->SetColor($x);
		}
		
		// Fill initial image
		ImageFill($this->image, 0, 0, $this->colors['bg']);
		
		// Generate pie segments
		for ($x=0; $x<sizeof($this->segment_data); $x++)
		{
			if ($this->segment_data[$x]['data'] > 0) {
				$this->CreateSegment($x);
			}
			
			$this->addLegendEntry($x);
		}
		
		return true;
	}
	
	function Draw()
	{
		// print Image
		header("Content-Type: image/png");
		imagePNG($this->image);

		return true;
	}
	
	function SetColor($cr)
	{
		$r = hexdec(substr($this->colors_data[$cr]['color'], 0, 2));
		$g = hexdec(substr($this->colors_data[$cr]['color'], 2, 2));
		$b = hexdec(substr($this->colors_data[$cr]['color'], 4, 2));

		$color = ImageColorAllocate($this->image, $r, $g, $b);
		return $color;
	}
	
	function CreateSegment($sr)
	{
		// generate color key
		$color = $this->colors['c'.$sr];
		$bordercolor = $this->colors['border'];
		
		if ($this->last_arc == 0) {
			ImageFilledEllipse($this->image, $this->center_x, $this->center_y, 
					$this->circle_width+2, $this->circle_height+2, $bordercolor);
		}
		
		// get combined data
		for ($x=0; $x<sizeof($this->segment_data); $x++)
		{
			$combined_value += $this->segment_data[$x][data];
		}
		
		// get segment percent
		$percent = ((double)$this->segment_data[$sr][data] / $combined_value);
		$start_arc = $this->last_arc;
		$end_arc = ((360.0 *  $percent) + $start_arc);

		if ($sr == sizeof($this->segment_data) - 1 ||
			$this->segment_data[$sr+1][data] == 0) {
			$end_arc = 360;
		}

		if ($end_arc > 360) {
			$end_arc = 360;
		}
		
		// calculate midpoint of segment
		/*$mid_arc = ($start_arc + $end_arc) / 2;
		
		if ($mid_arc >= 0 && $mid_arc <= 90) { 
			$mid_radius = round((($this->circle_width * (90 - $mid_arc)) + ($this->circle_height * $mid_arc)) / 180);
		} elseif ($mid_arc > 180 && $mid_arc <= 270) { 
			$mid_radius = round((($this->circle_width * (270 - $mid_arc)) + ($this->circle_height * ($mid_arc - 180))) / 180);
		} elseif ($mid_arc > 90 && $mid_arc <= 180) {
			$mid_radius = round((($this->circle_width * ($mid_arc - 90)) + ($this->circle_height * (180 - $mid_arc))) / 180);
		} elseif ($mid_arc > 270 && $mid_arc <= 360) {
			$mid_radius = round((($this->circle_width * ($mid_arc - 270)) + ($this->circle_height * (360 - $mid_arc))) / 180);
		}

		// Setup relative center point
		$relative_x = ($this->center_x + round(($mid_radius/100*$this->segment_data[$sr][exp]) * cos($mid_arc*pi()/180)));
		$relative_y = ($this->center_y + round(($mid_radius/100*$this->segment_data[$sr][exp]) * sin($mid_arc*pi()/180)));
		
		// Setup relative radius
		if ($start_arc >= 0 && $start_arc <= 90) { 
			$start_radius = ceil((($this->circle_width * (90 - $start_arc)) + ($this->circle_height * $start_arc)) / 180);
		} elseif ($start_arc > 180 && $start_arc <= 270) { 
			$start_radius = ceil((($this->circle_width * (270 - $start_arc)) + ($this->circle_height * ($start_arc - 180))) / 180);
		} elseif ($start_arc > 90 && $start_arc <= 180) {
			$start_radius = ceil((($this->circle_width * ($start_arc - 90)) + ($this->circle_height * (180 - $start_arc))) / 180);
		} elseif ($start_arc > 270 && $start_arc <= 360) {
			$start_radius = ceil((($this->circle_width * ($start_arc - 270)) + ($this->circle_height * (360 - $start_arc))) / 180);
		}

		if ($end_arc >= 0 && $end_arc <= 90) { 
			$end_radius = ceil((($this->circle_width * (90 - $end_arc)) + ($this->circle_height * $end_arc)) / 180);
		} elseif ($end_arc > 180 && $end_arc <= 270) { 
			$end_radius = ceil((($this->circle_width * (270 - $end_arc)) + ($this->circle_height * ($end_arc - 180))) / 180);
		} elseif ($end_arc > 90 && $end_arc <= 180) {
			$end_radius = ceil((($this->circle_width * ($end_arc - 90)) + ($this->circle_height * (180 - $end_arc))) / 180);
		} elseif ($end_arc > 270 && $end_arc <= 360) {
			$end_radius = ceil((($this->circle_width * ($end_arc - 270)) + ($this->circle_height * (360 - $end_arc))) / 180);
		}
				
		// get segment wall line coordinates
		$end_x[0] = round($relative_x + ($start_radius * cos($start_arc*pi()/180)));
		$end_y[0] = round($relative_y + ($start_radius * sin($start_arc*pi()/180)));
		$end_x[1] = round($relative_x + ($end_radius * cos($end_arc*pi()/180)));
		$end_y[1] = round($relative_y + ($end_radius * sin($end_arc*pi()/180)));
		
		$mid_x = (($relative_x + round($relative_x + ($mid_radius * cos($mid_arc*pi()/180)))) / 2);
		$mid_y = (($relative_y + round($relative_y + ($mid_radius * sin($mid_arc*pi()/180)))) / 2);*/
		
		// Draw Segment
		if (round($start_arc) != round($end_arc)) {
			ImageFilledArc($this->image, $this->center_x, $this->center_y, $this->circle_width, $this->circle_height,
				round($start_arc), round($end_arc), $color, IMG_ARC_PIE);
		}
		
		/*ImageFilledArc($this->image, $this->center_x, $this->center_y, $this->circle_width, $this->circle_height,
				$start_arc, $end_arc, $bordercolor, IMG_ARC_NOFILL);
		ImageEllipse($this->image, $this->center_x, $this->center_y, $this->circle_width, $this->circle_height,
				$bordercolor);*/
				
		//ImageArc($this->image, $relative_x, $relative_y, $this->circle_width, $this->circle_height,
		//		 $start_arc, $end_arc, $bordercolor);
		//ImageArc($this->image, $relative_x, $relative_y, $this->circle_width, $this->circle_width,
		//		 0, 360, $bordercolor);
		//ImageLine($this->image, $relative_x, $relative_y, $end_x[0], $end_y[0], $bordercolor);
		//ImageLine($this->image, $relative_x, $relative_y, $end_x[1], $end_y[1], $bordercolor);
		
		// Fill Color
		//ImageFillToBorder($this->image, $mid_x, $mid_y, $bordercolor, $color);
		
		if ($end_arc == 360) {
			ImageEllipse($this->image, $this->center_x, $this->center_y, $this->circle_width, $this->circle_height,
				$bordercolor);
		}

		$this->last_arc = $end_arc;

		return true;
	}
	
	function addLegendEntry ($sr) {
		$x = $this->center_x + ($this->circle_width / 2) + 20;
		$y = $this->center_y - ($this->circle_height / 2) + 10 + (30 * $sr);
		ImageFilledRectangle($this->image, $x, $y, $x+20, $y+20, $this->colors['c'.$sr]);
		ImageRectangle($this->image, $x, $y, $x+20, $y+20, $this->colors['border']);
		
		$name = $this->segment_data[$sr]['name'];
		$name .= " (" . $this->segment_data[$sr]['data'] . ")";
		
		for ($i=0; $i<strlen($name); $i++) {
			$c = $name{$i};
			Imagechar($this->image, 2, $x+30+($i*6), $y+5, $c, $this->colors['border']);
		}
	}
}

/*$data = array('center_x' => 200,
			  'center_y' => 200,
			  'circle_width' => 300,
			  'circle_height' => 300,
			  'image_width' => 400,
			  'image_height' => 400,
			  'colors_data' => array(array('name' => 'border', 'color' => '000000'),
			  						 array('name' => 'bg', 'color' => 'FFFFFF'),
			  						 array('name' => 'c0', 'color' => 'A3C8E7'),
			  						 array('name' => 'c1', 'color' => '528CBD'),
			  						 array('name' => 'c2', 'color' => '3B65AA'),
			  						 array('name' => 'c3', 'color' => '888888'),
			  						 array('name' => 'c4', 'color' => '666666')),
			  'split_chart' => 0,
			  'segment_data' => array(array('data' => 20, 'exp' => 0, 'border' => 1),
			  						  array('data' => 50, 'exp' => 0, 'border' => 1),
			  						  array('data' => 80, 'exp' => 0, 'border' => 1)));*/

//$chart = new piechart();
//$chart->init(serialize($data));
//$chart->build();
//$chart->draw();
/*echo "<pre>";
print_r($chart);
echo "</pre>";*/

?>