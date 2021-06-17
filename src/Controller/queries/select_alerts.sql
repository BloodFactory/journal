DECLARE @now     DATETIME = getdate()
DECLARE @user_id INT = ?

SELECT [a].[id],
       [a].[message],
       [a].[once]
FROM [alert] [a]
WHERE ([a].[begin_at] IS NULL OR [a].[begin_at] <= @now)
  AND ([a].[end_at] IS NULL OR [a].[end_at] >= @now)
  AND ([a].[once] != 1 OR NOT exists(SELECT * FROM [user_alert] [ua] WHERE [ua].[alert_id] = [a].[id] AND [ua].[usr_id] = @user_id))
