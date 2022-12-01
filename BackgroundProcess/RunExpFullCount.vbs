Set WshShell = CreateObject("WScript.Shell")
WshShell.Run chr(34) & "C:\Program Files\AdaSoft\AdaPos3.0Hpm\AdaPos\CFMExport.exe" & Chr(34), 0
Set WshShell = Nothing