<?php
	
	/**
	 * ImageCompare
	 *
	 * @author Benedict Reuthlinger
	 */
	class ImageCompare
	{
		private $debugmode  = false;
		
		private $image1o    = "";
		
		private $image2o    = "";
		
		private $image1;
		
		private $xdim1      = 0;
		
		private $ydim1      = 0;
		
		private $image2;
		
		private $xdim2      = 0;
		
		private $ydim2      = 0;
		
		private $colordiff  = 10;
		
		private $prozent    = 70;
		
		private $allpixel   = 0;
		
		private $truepixel  = 0;
		
		private $falsepixel = 0;
		
		/**
		 * Konstruktor
		 *
		 * @param $image1
		 * @param $image2
		 * @throws Exception
		 */
		public function __construct($image1, $image2)
		{
			$this->image1o = $image1;
			$this->image2o = $image2;
			
			// Image1
			$img1         = $this->getImageInfos($image1);
			$this->image1 = $img1['img'];
			$this->xdim1  = $img1['xdim'];
			$this->ydim1  = $img1['ydim'];
			
			// Image2
			$img2         = $this->getImageInfos($image2);
			$this->image2 = $img2['img'];
			$this->xdim2  = $img2['xdim'];
			$this->ydim2  = $img2['ydim'];
		}
		
		/**
		 * Prozent der übereinstimmenden Pixel zurückgeben
		 */
		public function getProzent()
		{
			return $this->prozent;
		}
		
		/**
		 * Prozent der übereinstimmenden Pixel setzen
		 *
		 * @param $p
		 */
		public function setProzent($p)
		{
			$this->prozent = $p;
		}
		
		/**
		 * Gibt die Anzahl aller Pixel des ersten Bildes zurück
		 */
		public function getAllPixel()
		{
			return $this->allpixel;
		}
		
		/**
		 * Gibt die Anzahl der übereinstimmenden Pixel zurück
		 */
		public function getTruePixel()
		{
			return $this->truepixel;
		}
		
		/**
		 * Gibt die Anzahl der nicht übereinstimmenden Pixel zurück
		 */
		public function getFalsePixel()
		{
			return $this->falsepixel;
		}
		
		/**
		 * compare
		 */
		public function compare()
		{
			$this->allpixel = $this->xdim1 * $this->ydim1;
			$nc             = 0;
			$yc             = 0;
			$dl             = 0;
			
			for ($x = 1; $x <= $this->xdim1; $x++)
			{
				for ($y = 1; $y <= $this->ydim1; $y++)
				{
					// Img1
					$rgb = imagecolorat($this->image1, $x, $y);
					$ret = $this->int2rgba($rgb);
					$r1  = $ret['r'];
					$g1  = $ret['g'];
					$b1  = $ret['b'];
					$a1  = $ret['a'];
					
					// Img2
					$rgb = imagecolorat($this->image2, $x, $y);
					$ret = $this->int2rgba($rgb);
					$r2  = $ret['r'];
					$g2  = $ret['g'];
					$b2  = $ret['b'];
					$a2  = $ret['a'];
					
					// Compare
					if ((($r2 - $this->colordiff) <= $r1 && ($r2 + $this->colordiff) >= $r1) && (($g2 - $this->colordiff) <= $g1 && ($g2 + $this->colordiff) >= $g1) && (($b2 - $this->colordiff) <= $b1 && ($b2 + $this->colordiff) >= $b1) && (($a2 - $this->colordiff) <= $a1 && ($a2 + $this->colordiff) >= $a1))
					{
						$yc++;
						$this->debugprint("y" . $r1 . "." . $g1 . "." . $b1 . "." . $a1 . ":" . $r2 . "." . $g2 . "." . $b2 . "." . $a2);
					}
					else
					{
						$nc++;
						$this->debugprint("n" . $r1 . "." . $g1 . "." . $b1 . "." . $a1 . ":" . $r2 . "." . $g2 . "." . $b2 . "." . $a2);
					}
					
					$dl++;
				}
			}
			
			$this->debugprint($dl . " Durchlaeufe ");
			
			$this->truepixel  = $yc;
			$this->falsepixel = $nc;
			
			$proz = 100 / $this->allpixel * $yc;
			if ($proz >= $this->prozent)
				return true;
			
			return false;
		}
		
		/**
		 * getImageInfos
		 *
		 * @param $image
		 * @return mixed
		 * @throws Exception
		 */
		private function getImageInfos($image)
		{
			$size = @getimagesize($image);
			$img  = null;
			
			if (empty($size))
				throw new Exception('Image "' . $image . '" not supported');
			
			if ($size['mime'] == 'image/jpeg')
				$img = imageCreateFromJPEG($image);
			if ($size['mime'] == 'image/gif')
				$img = imageCreateFromGIF($image);
			if ($size['mime'] == 'image/png')
				$img = imageCreateFromPNG($image);
			if ($size['mime'] == 'image/wbmp')
				$img = imageCreateFromWBMP($image);
			
			$ret['img']  = $img;
			$ret['xdim'] = imagesx($img);
			$ret['ydim'] = imagesy($img);
			
			return $ret;
		}
		
		/**
		 * Integer zu RGBA umwandeln
		 *
		 * @param $int
		 * @return array
		 */
		private function int2rgba($int)
		{
			$a = ($int >> 24) & 0xFF;
			$r = ($int >> 16) & 0xFF;
			$g = ($int >> 8) & 0xFF;
			$b = $int & 0xFF;
			return array(
				'r' => $r,
				'g' => $g,
				'b' => $b,
				'a' => $a
			);
		}
		
		/**
		 * debugprint
		 *
		 * @param $str
		 */
		private function debugprint($str)
		{
			if ($this->debugmode === true)
				echo $str . "\n";
		}
	}