<?php
// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

declare(strict_types=1);

use \EternalTerror\ViewHelpers;

final class SelectFieldTest extends WP_UnitTestCase
{
    public function testEmptySelectField()
    {
        $res = ViewHelpers\select_field("fooselect");
        $this->assertStringStartsWith('<select name="fooselect">', $res);
        $this->assertStringEndsWith('</select>', $res);
    }

    public function testSelectFieldWithOneOption()
    {
        $res = ViewHelpers\select_field("fooselect", [[42, 'An option']]);
        $this->assertStringStartsWith('<select name="fooselect">', $res);
        $this->assertStringEndsWith('</select>', $res);
        $this->assertStringContainsString('<option value="42">An option</option>', $res);
    }

    public function testSelectFieldWithMultipleOption()
    {
        $opts = [[42, 'An option'], [666, 'Another option'], ["foo", 'A foo option']];

        $res = ViewHelpers\select_field("fooselect", $opts);

        $this->assertStringStartsWith('<select name="fooselect">', $res);
        $this->assertStringEndsWith('</select>', $res);
        $this->assertStringContainsString('<option value="42">An option</option>', $res);
        $this->assertStringContainsString('<option value="666">Another option</option>', $res);
        $this->assertStringContainsString('<option value="foo">A foo option</option>', $res);
    }

    public function testSelectFieldWithSelectedOption()
    {
        $opts = [[42, 'An option'], [666, 'Another option'], ["foo", 'A foo option']];

        $res = ViewHelpers\select_field("fooselect", $opts, 666);

        $this->assertStringStartsWith('<select name="fooselect">', $res);
        $this->assertStringEndsWith('</select>', $res);
        $this->assertStringContainsString('<option value="42">An option</option>', $res);
        $this->assertStringContainsString('<option value="666" selected=\'selected\'>Another option</option>', $res);
        $this->assertStringContainsString('<option value="foo">A foo option</option>', $res);
    }
}
