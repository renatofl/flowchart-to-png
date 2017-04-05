<?php

use PHPUnit\Framework\TestCase;
use RNT\FlowChartImage;

class FlowChartImageTest extends TestCase
{
    public function testToPngShouldGenerateCorrectly()
    {
        $expected = $this->expectedBase64();
        $image = new FlowChartImage();
        $image->setContent($this->simpleFlowchart());
        $image->generate();
        ob_start();
            $image->toPng();
            $generatedImage = ob_get_contents();
        ob_end_clean();
        $result = base64_encode($generatedImage);
        $this->assertEquals($expected, $result);
    }

    protected function simpleFlowchart()
    {
        $flowchart = [
            "nodes" => [
                [
                    "id" =>  "flowchartStart",
                    "type" =>  "start",
                    "text" =>  "Start",
                    "left" =>  "20px",
                    "top" =>  "180px",
                    "countSource" =>  null
                ],
                [
                    "id" =>  "flowchartEnd",
                    "type" =>  "end",
                    "text" =>  "End",
                    "left" =>  "940px",
                    "top" =>  "180px",
                    "countSource" =>  null
                ],
                [
                    "id" =>  "flowchartWindow1489779664638",
                    "type" =>  "action",
                    "text" =>  "Approve",
                    "left" =>  "680px",
                    "top" =>  "260px",
                    "action" =>  "Approve",
                    "extraParams" =>  "",
                    "countSource" =>  "1"
                ],
                [
                    "id" =>  "flowchartWindow1489779672763",
                    "type" =>  "action",
                    "text" =>  "Reject",
                    "left" =>  "620px",
                    "top" =>  "40px",
                    "action" =>  "Reject",
                    "extraParams" =>  "",
                    "countSource" =>  "1"
                ]
            ],
            "edges" =>  [
                [
                    "source" =>  "flowchartStart",
                    "target" =>  "flowchartWindow1489779664638",
                    "data" =>  [
                        "label" =>  "",
                        "positionSource" =>  "RightMiddle",
                        "positionTarget" =>  "LeftMiddle"
                    ]
                ],
                [
                    "source" =>  "flowchartStart",
                    "target" =>  "flowchartWindow1489779672763",
                    "data" =>  [
                        "label" =>  "",
                        "positionSource" =>  "RightMiddle",
                        "positionTarget" =>  "LeftMiddle"
                    ]
                ],
                [
                    "source" =>  "flowchartWindow1489779672763",
                    "target" =>  "flowchartEnd",
                    "data" =>  [
                        "label" =>  "Success",
                        "return" =>  "success",
                        "positionSource" =>  "RightMiddle",
                        "positionTarget" =>  "LeftMiddle"
                    ]
                ],
                [
                    "source" =>  "flowchartWindow1489779664638",
                    "target" =>  "flowchartEnd",
                    "data" =>  [
                    "label" =>  "Success",
                        "return" =>  "success",
                        "positionSource" =>  "RightMiddle",
                        "positionTarget" =>  "LeftMiddle"
                    ]
                ]
            ]
        ];
        return json_encode($flowchart);
    }

    protected function expectedBase64()
    {
        return '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';
    }
}
