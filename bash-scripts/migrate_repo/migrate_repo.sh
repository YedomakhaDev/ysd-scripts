#!/bin/bash

################################################################################
# Git Repository Migration Script
#
# This script migrates a Git repository from GitLab to GitHub by performing
# the following steps:
# 1. Cloning the GitLab repository as a bare repository.
# 2. Adding a new remote repository on GitHub.
# 3. Pushing all branches and tags to the GitHub repository.
# 4. Cleaning up the temporary files after migration.
#
# Usage:
#   ./migrate_repo.sh <GitLab_Repo_URL> <GitHub_Repo_URL>
#
# Example:
#   ./migrate_repo.sh https://gitlab.com/user/repo.git https://github.com/user/repo.git
#
# Requirements:
# - Git must be installed and available in the system path.
# - You must have access to both the GitLab and GitHub repositories.
# - Proper authentication (e.g., SSH keys or personal access tokens) should be set up.
################################################################################

# Exit script on error
set -e

# Function to display usage instructions
usage() {
    echo "Usage: $0 <GitLab_Repo_URL> <GitHub_Repo_URL>"
    exit 1
}

# Check if two arguments are provided
if [ "$#" -ne 2 ]; then
    echo "Error: Two arguments required (GitLab repo URL and GitHub repo URL)."
    usage
fi

# Variables for repository URLs
GITLAB_REPO="$1"
GITHUB_REPO="$2"
TEMP_DIR="migrate_repo"

# Check if Git is installed
if ! command -v git &> /dev/null; then
    echo "Error: Git is not installed. Please install Git and try again."
    exit 1
fi

# Clone the repository from GitLab
echo "Cloning repository from GitLab: $GITLAB_REPO..."
if git clone --bare "$GITLAB_REPO" "$TEMP_DIR"; then
    echo "Successfully cloned repository."
else
    echo "Error: Failed to clone repository from GitLab."
    exit 1
fi

# Navigate to the repository folder
cd "$TEMP_DIR"

# Add new remote repository on GitHub
echo "Adding remote repository on GitHub: $GITHUB_REPO..."
if git remote add github "$GITHUB_REPO"; then
    echo "Remote added successfully."
else
    echo "Error: Failed to add GitHub remote."
    exit 1
fi

# Push the contents to the GitHub repository
echo "Pushing to GitHub repository..."
if git push --mirror github; then
    echo "Successfully pushed to GitHub."
else
    echo "Error: Failed to push to GitHub."
    exit 1
fi

# Return to the root directory and remove the temporary folder
cd ..
rm -rf "$TEMP_DIR"

echo "Migration completed successfully!"
