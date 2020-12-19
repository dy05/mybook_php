--
-- Structure de la table `books`
--

CREATE TABLE `books` (
                         `id_book` int(11) NOT NULL,
                         `name_book` varchar(255) NOT NULL,
                         `description_book` longtext,
                         `id_edition_book` int(11) NOT NULL,
                         `id_author_book` int(11) NOT NULL,
                         `couverture_book` varchar(255) DEFAULT NULL,
                         `filename` varchar(255) NOT NULL,
                         `edited_at` date NOT NULL,
                         `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Structure de la table `editions`
--

CREATE TABLE `editions` (
                            `id_edition` int(11) NOT NULL,
                            `name_edition` varchar(255) NOT NULL,
                            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `editions`
--

INSERT INTO `editions` (`id_edition`, `name_edition`, `created_at`) VALUES
(1, 'Edition cle', '2020-12-15 22:08:20'),
(2, '3ag edition', '2020-12-15 22:08:20'),
(3, 'ugobok', '2020-12-15 22:08:20');

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
                         `id_role` int(11) NOT NULL,
                         `name_role` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id_role`, `name_role`) VALUES
(1, 'admin'),
(2, 'user'),
(3, 'author');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
                         `id_user` int(11) NOT NULL,
                         `name_user` varchar(255) DEFAULT NULL,
                         `email_user` varchar(255) DEFAULT NULL,
                         `password_user` varchar(255) DEFAULT NULL,
                         `id_role_user` int(11) NOT NULL,
                         `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id_user`, `name_user`, `email_user`, `password_user`, `id_role_user`, `created_at`) VALUES
(1, NULL, 'admin@admin.com', '$2y$10$09JjvNIjabEjVxPkOaaU8OXJjRIyyn7r9mMpBdD1eM6V87wZez2ca', 1, '2020-12-15 12:24:07');

--
-- Index pour la table `books`
--
ALTER TABLE `books`
    ADD PRIMARY KEY (`id_book`),
    ADD UNIQUE KEY `name_unique` (`name_book`,`id_edition_book`,`edited_at`,`id_author_book`) USING BTREE,
    ADD KEY `author_book_foreign` (`id_author_book`),
    ADD KEY `edition_book_foreign` (`id_edition_book`);

--
-- Index pour la table `editions`
--
ALTER TABLE `editions`
    ADD PRIMARY KEY (`id_edition`),
    ADD UNIQUE KEY `name_edition` (`name_edition`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
    ADD PRIMARY KEY (`id_role`),
    ADD UNIQUE KEY `name_role` (`name_role`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
    ADD PRIMARY KEY (`id_user`),
    ADD UNIQUE KEY `email_user` (`email_user`),
    ADD UNIQUE KEY `name_user` (`name_user`),
    ADD KEY `role_user_foreign` (`id_role_user`);

--
-- AUTO_INCREMENT pour la table `books`
--
ALTER TABLE `books`
    MODIFY `id_book` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `editions`
--
ALTER TABLE `editions`
    MODIFY `id_edition` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
    MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
    MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Contraintes pour la table `books`
--
ALTER TABLE `books`
    ADD CONSTRAINT `author_book_foreign` FOREIGN KEY (`id_author_book`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
    ADD CONSTRAINT `edition_book_foreign` FOREIGN KEY (`id_edition_book`) REFERENCES `editions` (`id_edition`) ON DELETE CASCADE;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
    ADD CONSTRAINT `role_user_foreign` FOREIGN KEY (`id_role_user`) REFERENCES `roles` (`id_role`) ON DELETE CASCADE;
COMMIT;
