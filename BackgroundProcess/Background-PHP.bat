@REM @echo off
@REM Title BackgroundPHP
@REM "C:\xampp\php\php.exe"  index.php cConsoleDocument FSxMQListener 
@REM pause

@echo off

IF EXIST "C:\php\php.exe" (
    set phppath=C:\php\php.exe
) ELSE (
    set phppath=C:\xampp\php\php.exe
)

taskkill /FI "WindowTitle eq BackPrcPHP*" /T /F
Title BackPrcPHP
%phppath%  index.php cConsoleDocument FSxMQListener

pause