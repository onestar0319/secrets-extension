<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Ambrose Larkin <ambroseLarkin@goldendelta.space>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Secrets\Tests\Unit\Controller;

use OCA\Secrets\Controller\SecretApiController;
use PHPUnit\Framework\TestCase;

/** @ignore */
class NoteApiControllerTest extends TestCase {
	public function setUp(): void {
		parent::setUp();
		$this->controller = new SecretApiController($this->request, $this->service, $this->userId);
	}
}
