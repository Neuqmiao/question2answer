<?php
/*
	Question2Answer by Gideon Greenspan and contributors
	http://www.question2answer.org/

	File: qa-include/Q2A/Recalc/BlobsToDiskMove.php
	Description: Recalc processing class for the blobs to disk process.


	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	More about this license: http://www.question2answer.org/license.php
*/


if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}

class Q2A_Recalc_BlobsToDiskMove extends Q2A_Recalc_AbstractStep
{
	public function doStep()
	{
		$blob = qa_db_get_next_blob_in_db($this->state->next);

		if (!isset($blob)) {
			$this->state->transition('doblobstodisk_complete');
			return false;
		}

		require_once QA_INCLUDE_DIR . 'app/blobs.php';
		require_once QA_INCLUDE_DIR . 'db/blobs.php';

		if (qa_write_blob_file($blob['blobid'], $blob['content'], $blob['format'])) {
			qa_db_blob_set_content($blob['blobid'], null);
		}

		$this->state->next = 1 + $blob['blobid'];
		$this->state->done++;
		return true;
	}

	public function getMessage()
	{
		return $this->progressLang('admin/blobs_move_moved', $this->state->done, $this->state->length);
	}
}
