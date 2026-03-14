-- =============================================
-- BASE DE DONNÉES : FORUM
-- À importer dans phpMyAdmin
-- =============================================

-- Table utilisateur
CREATE TABLE IF NOT EXISTS utilisateur (
    user_id       INT AUTO_INCREMENT PRIMARY KEY,
    login         VARCHAR(50) NOT NULL UNIQUE,
    password      VARCHAR(255) NOT NULL,
    date_naissance DATE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table questions
CREATE TABLE IF NOT EXISTS questions (
    q_id          INT AUTO_INCREMENT PRIMARY KEY,
    q_titre       VARCHAR(50) NOT NULL,
    q_contenu     VARCHAR(150) NOT NULL,
    user_id       INT NOT NULL,
    q_date_ajout  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES utilisateur(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table reponse
CREATE TABLE IF NOT EXISTS reponse (
    r_id              INT AUTO_INCREMENT PRIMARY KEY,
    r_contenu         TEXT NOT NULL,
    r_fk_question_id  INT NOT NULL,
    user_id           INT NOT NULL,
    r_date_ajout      DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (r_fk_question_id) REFERENCES questions(q_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES utilisateur(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- Données de test (optionnel)
-- =============================================

INSERT INTO utilisateur (login, password, date_naissance) VALUES
('admin', MD5('admin123'), '2000-01-01'),
('testuser', MD5('test123'), '2001-06-15');

INSERT INTO questions (q_titre, q_contenu, user_id) VALUES
('Comment apprendre PHP ?', 'Je débute en PHP et je cherche des ressources pour progresser rapidement. Avez-vous des conseils ?', 1),
('Différence entre GET et POST ?', 'Je ne comprends pas bien quand utiliser $_GET et quand utiliser $_POST en PHP. Quelqu''un peut expliquer ?', 2);

INSERT INTO reponse (r_contenu, r_fk_question_id, user_id) VALUES
('Je recommande de commencer par les tutoriels officiels sur php.net, puis de pratiquer avec de petits projets comme un forum ou un CRUD simple.', 1, 2),
('GET envoie les données dans l''URL (visible), POST les envoie dans le corps de la requête (invisible). On utilise POST pour les formulaires sensibles comme les mots de passe.', 2, 1);