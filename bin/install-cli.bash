#!/bin/bash
# ====================================================
#  Script d'installation du CLI Talaria
# ====================================================

# Chemin absolu du projet (le dossier parent de bin)
PROJ_PATH="$(cd "$(dirname "$0")"/.. && pwd)"

# Chemin absolu vers le binaire PHP
CLI_PATH="$PROJ_PATH/bin/console.php"

# Nom de la commande (ce sera ton alias)
ALIAS_NAME="talaria"

# Fichier shell courant (bash ou zsh)
SHELL_RC="$HOME/.bashrc"
if [ -n "$ZSH_VERSION" ]; then
    SHELL_RC="$HOME/.zshrc"
fi

echo "🚀 Installation de l'alias CLI '$ALIAS_NAME'..."
echo "➡️  Script PHP : $CLI_PATH"
echo "➡️  Fichier de configuration shell : $SHELL_RC"

# Vérifie si l'alias existe déjà
if grep -q "alias $ALIAS_NAME=" "$SHELL_RC"; then
    echo "ℹ️  Alias '$ALIAS_NAME' déjà présent dans $SHELL_RC"
else
    echo "alias $ALIAS_NAME='php $CLI_PATH'" >> "$SHELL_RC"
    echo "✅ Alias '$ALIAS_NAME' ajouté à $SHELL_RC"
fi

# Rend le binaire exécutable
chmod +x "$CLI_PATH"

# Recharge la configuration du shell (facultatif)
echo "♻️  Recharge du shell..."
source "$SHELL_RC"

echo "✅ Installation terminée !"
echo "Vous pouvez maintenant exécuter :"
echo "    $ALIAS_NAME generateEntities users"