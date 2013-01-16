<?php

class NotificationHelper
{
	static function markProjectRead($user_id, $project_id)
	{
		$notifications = Notification::find('user_id = "' . $user_id . '" AND project_id = "' . $project_id . '" AND task_id IS NULL AND comment_id IS NULL AND note_id IS NULL AND upload_id IS NULL');

		foreach ($notifications AS $notification) {
			$notification->read = 1;
			$notification->save();
		}
	}

	static function markTaskRead($user_id, $project_id, $task_id)
	{
		$notifications = Notification::find('user_id = "' . $user_id . '" AND project_id = "' . $project_id . '" AND task_id = "' . $task_id . '"');

		foreach ($notifications AS $notification) {
			$notification->read = 1;
			$notification->save();
		}
	}

	static function updateCommentNotification($project, $task, $comment)
	{
		$commentUser = $comment->getUser();

		foreach ($task->getTaskUser() AS $taskUser) {
			if ($comment->user_id != $taskUser->user_id) {
				$notification = new Notification();

				$notification->user_id = $taskUser->user_id;
				$notification->message = '<strong>' . $commentUser->full_name . '</strong> updated comment on your task <strong>' . $task->title . '</strong> : "' . substr(strip_tags($comment->comment), 0, 200) . '..."';
				$notification->project_id = $task->project_id;
				$notification->task_id = $comment->task_id;
				$notification->comment_id = $comment->id;
				$notification->read = 0;
				$notification->created_by = $comment->user_id;
				$notification->created_at = new Phalcon\Db\RawValue('now()');

				$notification->save();
			}
		}
	}
}