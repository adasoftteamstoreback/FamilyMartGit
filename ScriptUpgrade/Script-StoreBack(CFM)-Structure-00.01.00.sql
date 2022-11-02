
/* Napat(Jame) 18/10/2022 ลบ PK ตาราง DTCut ตาม DT */
IF EXISTS(SELECT name FROM sys.key_constraints WHERE type = 'PK' AND OBJECT_NAME(parent_object_id) = N'TCNTPdtChkDTCut') BEGIN
	ALTER TABLE TCNTPdtChkDTCut DROP CONSTRAINT PK__TCNTPdtC__981CB52172D1C499
END
GO

/* Napat(Jame) 27/10/2022 สคริปสร้างตารางเก็บเหตุผลจากพี่บั้ม */
SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[TCNMRsn](
	[FTRsnCode] [varchar](5) NOT NULL,
	[FTRsnDescTh] [varchar](100) NULL,
	[FTRsnDescEn] [varchar](100) NULL,
	[FTRemark] [varchar](255) NULL,
	[FDDateUpd] [datetime] NULL,
	[FTTimeUpd] [varchar](8) NULL,
	[FTWhoUpd] [varchar](50) NULL,
	[FDDateIns] [datetime] NULL,
	[FTTimeIns] [varchar](8) NULL,
	[FTWhoIns] [varchar](50) NULL,
PRIMARY KEY CLUSTERED 
(
	[FTRsnCode] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO

/* Napat(Jame) 27/10/2022 สคริปเพิ่มข้อมูลเหตุผล */
INSERT INTO TCNMRsn (FTRsnCode,FTRsnDescTh,FTRsnDescEn,FTRemark,FDDateUpd,FTTimeUpd,FTWhoUpd,FDDateIns,FTTimeIns,FTWhoIns)
VALUES ('001','ไม่พบสินค้าในระบบ','Not Found Product Master','',CONVERT(VARCHAR(10), GETDATE(), 121),CONVERT(VARCHAR(8), GETDATE(), 108),'adasoft',CONVERT(VARCHAR(10), GETDATE(), 121),CONVERT(VARCHAR(8), GETDATE(), 108),'adasoft')
,('002','ไม่พบหน่วยในระบบ','Not found product unit master','',CONVERT(VARCHAR(10), GETDATE(), 121),CONVERT(VARCHAR(8), GETDATE(), 108),'adasoft',CONVERT(VARCHAR(10), GETDATE(), 121),CONVERT(VARCHAR(8), GETDATE(), 108),'adasoft')
,('003','ไม่พบบาร์โค้ดระบบ','Not found product barcode master','',CONVERT(VARCHAR(10), GETDATE(), 121),CONVERT(VARCHAR(8), GETDATE(), 108),'adasoft',CONVERT(VARCHAR(10), GETDATE(), 121),CONVERT(VARCHAR(8), GETDATE(), 108),'adasoft')
,('004','สินค้าฝากขาย','Consignment goods','',CONVERT(VARCHAR(10), GETDATE(), 121),CONVERT(VARCHAR(8), GETDATE(), 108),'adasoft',CONVERT(VARCHAR(10), GETDATE(), 121),CONVERT(VARCHAR(8), GETDATE(), 108),'adasoft')
,('005','สินค้าไม่อนุญาตขาย','Products not allowed sell','',CONVERT(VARCHAR(10), GETDATE(), 121),CONVERT(VARCHAR(8), GETDATE(), 108),'adasoft',CONVERT(VARCHAR(10), GETDATE(), 121),CONVERT(VARCHAR(8), GETDATE(), 108),'adasoft')