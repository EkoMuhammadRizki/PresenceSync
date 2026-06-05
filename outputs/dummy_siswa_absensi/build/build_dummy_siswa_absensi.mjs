import { SpreadsheetFile, Workbook } from "@oai/artifact-tool";

const outputDir = "D:/PresenceSync/outputs/dummy_siswa_absensi";
const outputPath = `${outputDir}/dummy_siswa_absensi.xlsx`;

const headers = [
  "Nama",
  "NISN",
  "NIS",
  "Kelas",
  "Jenis Kelamin (L/P)",
  "Tanggal Lahir (YYYY-MM-DD)",
  "Alamat",
];

const students = [
  ["Andi Saputra", "0081234501", "13001", "X-1", "L", "2009-04-12", "Jl. Melati No. 14, Bandung"],
  ["Rini Lestari", "0081234502", "13002", "XI-1", "P", "2008-09-23", "Jl. Diponegoro No. 27, Yogyakarta"],
  ["Bima Pratama", "0081234503", "13003", "XII-1", "L", "2007-11-05", "Jl. Ahmad Yani No. 8, Surabaya"],
  ["Siti Aulia Rahma", "0081234504", "13004", "X-1", "P", "2009-01-18", "Jl. Kenanga No. 33, Semarang"],
  ["Fajar Nugroho", "0081234505", "13005", "XI-1", "L", "2008-06-30", "Jl. Sudirman No. 21, Jakarta"],
  ["Dewi Anggraini", "0081234506", "13006", "XII-1", "P", "2007-03-09", "Jl. Pahlawan No. 45, Malang"],
  ["Rizky Ramadhan", "0081234507", "13007", "X-1", "L", "2009-12-02", "Jl. Mawar No. 10, Bogor"],
  ["Nabila Putri", "0081234508", "13008", "XI-1", "P", "2008-08-14", "Jl. Gatot Subroto No. 19, Medan"],
  ["Yoga Firmansyah", "0081234509", "13009", "XII-1", "L", "2007-05-26", "Jl. Cendana No. 6, Makassar"],
  ["Maya Kartika Sari", "0081234510", "13010", "X-1", "P", "2009-10-17", "Jl. Kartini No. 52, Denpasar"],
];

const workbook = Workbook.create();
const sheet = workbook.worksheets.add("Data Siswa");

sheet.getRange("A1:G11").values = [headers, ...students];
sheet.freezePanes.freezeRows(1);

sheet.getRange("A1:G1").format = {
  fill: "#1F4E78",
  font: { bold: true, color: "#FFFFFF" },
  borders: { preset: "all", style: "thin", color: "#B7C9D6" },
};
sheet.getRange("A2:G11").format = {
  borders: { preset: "all", style: "thin", color: "#D9E2EA" },
  wrapText: true,
};

sheet.getRange("A:A").format.columnWidthPx = 180;
sheet.getRange("B:B").format.columnWidthPx = 110;
sheet.getRange("C:C").format.columnWidthPx = 75;
sheet.getRange("D:D").format.columnWidthPx = 70;
sheet.getRange("E:E").format.columnWidthPx = 135;
sheet.getRange("F:F").format.columnWidthPx = 175;
sheet.getRange("G:G").format.columnWidthPx = 250;
sheet.getRange("A1:G1").format.rowHeightPx = 28;
sheet.getRange("A2:G11").format.rowHeightPx = 24;

sheet.getRange("B2:C11").setNumberFormat("@");
sheet.getRange("F2:F11").setNumberFormat("@");
sheet.getRange("D2:D101").dataValidation = { rule: { type: "list", values: ["X-1", "XI-1", "XII-1"] } };
sheet.getRange("E2:E101").dataValidation = { rule: { type: "list", values: ["L", "P"] } };

const inspect = await workbook.inspect({
  kind: "table",
  range: "Data Siswa!A1:G11",
  include: "values,formats",
  tableMaxRows: 12,
  tableMaxCols: 8,
});
console.log(inspect.ndjson);

const errors = await workbook.inspect({
  kind: "match",
  searchTerm: "#REF!|#DIV/0!|#VALUE!|#NAME\\?|#N/A",
  options: { useRegex: true, maxResults: 100 },
  summary: "final formula error scan",
});
console.log(errors.ndjson);

const preview = await workbook.render({
  sheetName: "Data Siswa",
  range: "A1:G11",
  scale: 1,
  format: "png",
});
console.log(`Rendered preview bytes: ${(await preview.arrayBuffer()).byteLength}`);

const xlsx = await SpreadsheetFile.exportXlsx(workbook);
console.log(`Prepared ${outputPath}`);
console.log(`XLSX_BASE64:${Buffer.from(xlsx.data).toString("base64")}`);
