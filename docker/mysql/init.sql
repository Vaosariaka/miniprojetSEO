CREATE TABLE IF NOT EXISTS articles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  summary TEXT NOT NULL,
  content LONGTEXT NOT NULL,
  image_path VARCHAR(255) DEFAULT 'assets/img/iran.png',
  image_alt VARCHAR(255) NOT NULL,
  meta_title VARCHAR(255) NOT NULL,
  meta_description VARCHAR(320) NOT NULL,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  published_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);



INSERT INTO articles (
  title,
  slug,
  summary,
  content,
  image_path,
  image_alt,
  meta_title,
  meta_description,
  is_published
)
SELECT
  'Guerre en Iran',
  'guerre-en-iran',
  'Conflit debutant le 28 fevrier 2026 par des frappes ciblees americano-israeliennes, suivi de represailles iraniennes dans toute la region.',
  '<h2>Contexte</h2><p>La guerre d''Iran debute le 28 fevrier 2026 avec une operation militaire conjointe americano-israelienne. Cote israelien, elle est nommee Operation Roaring Lion. Cote americain, elle prend le nom d''Operation Epic Fury.</p><h2>Escalade</h2><p>En reponse, l''Iran lance l''operation Promesse honnete 4, avec des frappes et des drones vers plusieurs cibles au Moyen-Orient, a Chypre et au Caucase.</p><h3>Facteurs declencheurs</h3><p>Le conflit eclate six semaines apres une repression violente de manifestations anti-gouvernementales en Iran. Les tensions regionales et la rupture diplomatique precipitent l''affrontement.</p><h2>Zones frappees</h2><p>Les premieres frappes visent notamment Teheran, Ispahan, Qom, Karadj et Kermanchah. La riposte iranienne touche aussi des bases militaires americaines dans la region et certaines infrastructures civiles.</p>',
  'assets/img/iran.png',
  'Illustration de guerre en iran',
  'Guerre en Iran - Analyse et chronologie',
  'Site d''information sur la guerre en Iran: contexte, chronologie, zones de conflit et consequences regionales.',
  1
WHERE NOT EXISTS (
  SELECT 1 FROM articles WHERE slug = 'guerre-en-iran'
);
