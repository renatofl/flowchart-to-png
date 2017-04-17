<?php

namespace Fidelize\Flowchart\ToPng;

class FlowChartImage
{
    protected $content;
    public $selectedAction;
    public $selectedColor;
    private $jsonData;
    private $imageHandle;
    private $boxColor;
    private $imageProperties;
    private $boxProperties;
    private $backgroundColor;

    public function generate()
    {
        $this->jsonData = json_decode(utf8_encode($this->content));
        $this->initImage();
        $this->drawEdges();
        $this->drawNodes();

        return $this;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function setSelectedAction($selectedAction)
    {
        $this->selectedAction = $selectedAction;
    }

    public function setSelectedColor($selectedColor)
    {
        $this->selectedColor = $selectedColor;
    }

    public function initImage()
    {
        $this->getImageSize();
        $this->imageHandle = imagecreatetruecolor($this->imageProperties['width'], $this->imageProperties['height']);
        imagesetthickness($this->imageHandle, 3);

        $this->boxProperties = ['height' => 100, 'width' => 100];
        $this->configureColors();

        imagefill($this->imageHandle, 0, 0, $this->backgroundColor);
    }

    private function getImageSize()
    {
        $height = 100;
        $width  = 100;
        foreach ($this->jsonData->nodes as $node) {
            $top  = str_replace('px', '', $node->top) + 150;
            $left = str_replace('px', '', $node->left) + 150;
            if (($top) > $height) {
                $height = $top;
            }
            if (($left) > $width) {
                $width = $left;
            }
        }

        $this->imageProperties = [
            'height' => $height,
            'width' => $width,
        ];
    }

    public function toPng($path = null)
    {
        imagepng($this->imageHandle, $path);
        imagedestroy($this->imageHandle);
    }

    public function getBase64()
    {
        ob_start();
        $this->generate()->toPng();
        $base64 = base64_encode(ob_get_contents());
        ob_end_clean();

        return $base64;
    }

    public function drawActionBox($top, $left, $text, $type, $nodeId)
    {
        $height  = $this->boxProperties['height'];
        $width   = $this->boxProperties['width'];
        $bgColor = $this->boxProperties['contentBg'];
        if ($nodeId == $this->selectedAction) {
            $bgColor = $this->boxProperties['contentSelectedBg'];
        }

        $border = 3;
        $radius = 10;
        if (in_array($type, ['start', 'end'])) {
            $radius = 45;
        }
        $black = imagecolorallocate($this->imageHandle, 0, 0, 0);
        $this->drawRoundrectangle($this->imageHandle, $left, $top, $left + $width, $top + $height, $radius,
            $this->boxColor, 1);
        $this->drawRoundrectangle($this->imageHandle, $left + $border, $top + $border, $left + $width - $border,
            $top + $height - $border, $radius, $bgColor, 1);

        $size = 5;
        if (strlen($text) > 10) {
            $size = 3;
        }
        if (strlen($text) > 15) {
            $size = 1;
        }

        $fw   = imagefontwidth($size);
        $l    = strlen($text);
        $tw   = $l * $fw;
        $iw   = 100;
        $xpos = ($iw - $tw) / 2;
        imagestring($this->imageHandle, $size, $left + $xpos, $top + 40, utf8_decode($text), $black);
    }

    public function drawConnector($originTop, $originLeft, $destinTop, $destinLeft, $text, $positionOrigin,
                                  $positionDestin)
    {
        $black             = imagecolorallocate($this->imageHandle, 0, 0, 0);
        $lineColor         = imagecolorallocate($this->imageHandle, 0, 100, 0);
        $connectorDistance = 40;

        $originTopNew  = $originTop;
        $originLeftNew = $originLeft;
        $destinTopNew  = $destinTop;
        $destinLeftNew = $destinLeft;
        imagefilledellipse($this->imageHandle, $originLeft, $originTop, 20, 20, $lineColor);

        if ($positionOrigin == 'RightMiddle') {
            imageline($this->imageHandle, $originLeft, $originTop, $originLeft + $connectorDistance, $originTop,
                $lineColor);
            $originLeftNew = $originLeft + $connectorDistance;
        }

        if ($positionOrigin == 'LeftMiddle') {
            imageline($this->imageHandle, $originLeft, $originTop, $originLeft - $connectorDistance, $originTop,
                $lineColor);
            $originLeftNew = $originLeft - $connectorDistance;
        }

        if ($positionOrigin == 'BottomCenter') {
            imageline($this->imageHandle, $originLeft, $originTop, $originLeft, $originTop + $connectorDistance,
                $lineColor);
            $originTopNew = $originTop + $connectorDistance;
        }

        if ($positionOrigin == 'TopCenter') {
            imageline($this->imageHandle, $originLeft, $originTop, $originLeft, $originTop - $connectorDistance,
                $lineColor);
            $originTopNew = $originTop - $connectorDistance;
        }

        if ($positionDestin == 'RightMiddle') {
            $this->arrow($this->imageHandle, $destinLeft, $destinTop, $destinLeft + $connectorDistance, $destinTop, 20,
                5, $lineColor);
            $destinLeftNew = $destinLeft + $connectorDistance;
        }

        if ($positionDestin == 'LeftMiddle') {
            $this->arrow($this->imageHandle, $destinLeft - $connectorDistance, $destinTop, $destinLeft, $destinTop, 15,
                5, $lineColor);
            $destinLeftNew = $destinLeft - $connectorDistance;
        }

        if ($positionDestin == 'RightMiddle') {
            imageline($this->imageHandle, $destinLeft, $destinTop, $destinLeft + $connectorDistance, $destinTop,
                $lineColor);
            $destinLeftNew = $destinLeft + $connectorDistance;
        }

        if ($positionDestin == 'LeftMiddle') {
            imageline($this->imageHandle, $destinLeft, $destinTop, $destinLeft - $connectorDistance, $destinTop,
                $lineColor);
            $destinLeftNew = $destinLeft - $connectorDistance;
        }

        $leftDiff = $originLeftNew - $destinLeftNew;
        if ($leftDiff < 0) {
            $middle        = $leftDiff / 2;
            imageline($this->imageHandle, $originLeftNew, $originTopNew, $originLeftNew - $middle, $originTopNew,
                $lineColor);
            imageline($this->imageHandle, $destinLeftNew, $destinTopNew, $destinLeftNew + $middle, $destinTopNew,
                $lineColor);
            $originLeftNew = $originLeftNew - $middle;
            $destinLeftNew = $destinLeftNew + $middle;
        }
        $topDiff = $originTopNew - $destinTopNew;
        if ($topDiff > 0) {
            $middle       = $topDiff / 2;
            imageline($this->imageHandle, $originLeftNew, $originTopNew, $originLeftNew, $originTopNew - $middle,
                $lineColor);
            imageline($this->imageHandle, $destinLeftNew, $destinTopNew, $destinLeftNew, $destinTopNew + $middle,
                $lineColor);
            $originTopNew = $originTopNew - $middle;
            $destinTopNew = $destinTopNew + $middle;
        }
        $leftDiff = $originLeftNew - $destinLeftNew;
        if ($leftDiff > 0) {
            $middle        = $leftDiff / 2;
            imageline($this->imageHandle, $originLeftNew, $originTopNew, $originLeftNew - $middle, $originTopNew,
                $lineColor);
            imageline($this->imageHandle, $destinLeftNew, $destinTopNew, $destinLeftNew + $middle, $destinTopNew,
                $lineColor);
            $originLeftNew = $originLeftNew - $middle;
            $destinLeftNew = $destinLeftNew + $middle;
        }
        $topDiff = $originTopNew - $destinTopNew;
        if ($topDiff < 0) {
            $middle       = $topDiff / 2;
            imageline($this->imageHandle, $originLeftNew, $originTopNew, $originLeftNew, $originTopNew - $middle,
                $lineColor);
            imageline($this->imageHandle, $destinLeftNew, $destinTopNew, $destinLeftNew, $destinTopNew + $middle,
                $lineColor);
            $originTopNew = $originTopNew - $middle;
            $destinTopNew = $destinTopNew + $middle;
        }

        imagestring($this->imageHandle, 5, $destinLeftNew - 40, $destinTopNew, utf8_decode($text), $black);
    }

    public function getPointConnectorFromNode($nodeId, $type)
    {
        $nodeLocated = null;
        foreach ($this->jsonData->nodes as $node) {
            if ($nodeId == $node->id) {
                $nodeLocated = $node;
            }
        }

        if (!$nodeLocated) {
            return [0, 0];
        }
        $top  = 0;
        $left = 0;

        if ($type == 'RightMiddle') {
            $top  = str_replace('px', '', $nodeLocated->top) + 50;
            $left = str_replace('px', '', $nodeLocated->left) + 100;
        }

        if ($type == 'LeftMiddle') {
            $top  = str_replace('px', '', $nodeLocated->top) + 50;
            $left = str_replace('px', '', $nodeLocated->left);
        }

        if ($type == 'BottomCenter') {
            $top  = str_replace('px', '', $nodeLocated->top) + 100;
            $left = str_replace('px', '', $nodeLocated->left) + 50;
        }

        if ($type == 'TopCenter') {
            $top  = str_replace('px', '', $nodeLocated->top);
            $left = str_replace('px', '', $nodeLocated->left) + 50;
        }


        return [$top, $left];
    }

    private function configureColors()
    {
        $this->backgroundColor                    = imagecolorallocate($this->imageHandle, 255, 255, 255);
        $this->boxColor                           = imagecolorallocate($this->imageHandle, 0, 200, 0);
        $this->boxProperties['contentBg']         = imagecolorallocate($this->imageHandle, 241, 241, 241);
        $this->boxProperties['contentSelectedBg'] = imagecolorallocate($this->imageHandle, $this->selectedColor[0],
            $this->selectedColor[1], $this->selectedColor[2]);
    }

    private function drawRoundrectangle($img, $x1, $y1, $x2, $y2, $radius, $color, $filled = 1)
    {
        if ($filled == 1) {
            imagefilledrectangle($img, $x1 + $radius, $y1, $x2 - $radius, $y2, $color);
            imagefilledrectangle($img, $x1, $y1 + $radius, $x1 + $radius - 1, $y2 - $radius, $color);
            imagefilledrectangle($img, $x2 - $radius + 1, $y1 + $radius, $x2, $y2 - $radius, $color);

            imagefilledarc($img, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, 180, 270, $color, IMG_ARC_PIE);
            imagefilledarc($img, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, 270, 360, $color, IMG_ARC_PIE);
            imagefilledarc($img, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, 90, 180, $color, IMG_ARC_PIE);
            imagefilledarc($img, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, 360, 90, $color, IMG_ARC_PIE);
        } else {
            imageline($img, $x1 + $radius, $y1, $x2 - $radius, $y1, $color);
            imageline($img, $x1 + $radius, $y2, $x2 - $radius, $y2, $color);
            imageline($img, $x1, $y1 + $radius, $x1, $y2 - $radius, $color);
            imageline($img, $x2, $y1 + $radius, $x2, $y2 - $radius, $color);

            imagearc($img, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, 180, 270, $color);
            imagearc($img, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, 270, 360, $color);
            imagearc($img, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, 90, 180, $color);
            imagearc($img, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, 360, 90, $color);
        }
    }

    private function arrow($im, $x1, $y1, $x2, $y2, $alength, $awidth, $color)
    {
        $distance = sqrt(pow($x1 - $x2, 2) + pow($y1 - $y2, 2));

        $dx = $x2 + ($x1 - $x2) * $alength / $distance;
        $dy = $y2 + ($y1 - $y2) * $alength / $distance;

        $k = $awidth / $alength;

        $x2o = $x2 - $dx;
        $y2o = $dy - $y2;

        $x3 = $y2o * $k + $dx;
        $y3 = $x2o * $k + $dy;

        $x4 = $dx - $y2o * $k;
        $y4 = $dy - $x2o * $k;

        imageline($im, $x1, $y1, $dx, $dy, $color);
        imagefilledpolygon($im, array($x2, $y2, $x3, $y3, $x4, $y4), 3, $color);
    }

    private function drawEdges()
    {
        foreach ($this->jsonData->edges as $edge) {
            $positionOrigin = $this->getPointConnectorFromNode($edge->source, $edge->data->positionSource);
            list($topOrigin, $leftOrigin) = $positionOrigin;

            $positionDestin = $this->getPointConnectorFromNode($edge->target, $edge->data->positionTarget);
            list($topDestin, $leftDestin) = $positionDestin;

            $this->drawConnector($topOrigin, $leftOrigin, $topDestin, $leftDestin, $edge->data->label,
            $edge->data->positionSource, $edge->data->positionTarget);
        }
    }

    private function drawNodes()
    {
        foreach ($this->jsonData->nodes as $node) {
            $top  = str_replace('px', '', $node->top);
            $left = str_replace('px', '', $node->left);
            $this->drawActionBox($top, $left, $node->text, $node->type, $node->id);
        }
    }
}
