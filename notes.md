jalankan untuk update data rencana realisasi

CREATE OR REPLACE VIEW rencana_realisasi_pemupukan AS
SELECT 
    r.regional,
    r.kebun,
    r.afdeling,
    SUM(CASE WHEN r.semester_pemupukan = 1 THEN r.jumlah_pupuk ELSE 0 END) AS rencana_semester_1,
    SUM(CASE WHEN r.semester_pemupukan = 1 THEN COALESCE(p.jumlah_pupuk, 0) ELSE 0 END) AS realisasi_semester_1,
    SUM(CASE WHEN r.semester_pemupukan = 2 THEN r.jumlah_pupuk ELSE 0 END) AS rencana_semester_2,
    SUM(CASE WHEN r.semester_pemupukan = 2 THEN COALESCE(p.jumlah_pupuk, 0) ELSE 0 END) AS realisasi_semester_2,
    SUM(r.jumlah_pupuk) AS rencana_total,
    SUM(COALESCE(p.jumlah_pupuk, 0)) AS realisasi_total
FROM rencana_pemupukan r
LEFT JOIN pemupukan p
    ON r.regional = p.regional
    AND r.kebun = p.kebun
    AND r.afdeling = p.afdeling
    AND r.tahun_tanam = p.tahun_tanam
    AND r.blok = p.blok
    AND r.jenis_pupuk = p.jenis_pupuk
    AND r.semester_pemupukan = (CASE WHEN EXTRACT(MONTH FROM p.tgl_pemupukan) <= 6 THEN 1 ELSE 2 END)
GROUP BY r.regional, r.kebun, r.afdeling;