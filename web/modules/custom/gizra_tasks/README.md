# Drupal Starter – Home Assignment

Welcome to the **Drupal Starter** project! This repository includes both **backend** and **frontend** tasks as part of a home assignment. Follow the instructions below to get started and complete the tasks.

---

## How to Start the Project

To set up the project locally and make all functionality work as expected, run the following commands:

```bash
# Clone the repository
git clone git@github.com:Hayk-web/drupal-starter.git

# Start the DDEV environment
ddev start

# Install PHP dependencies
ddev composer install

# Copy the local config template
cp .ddev/config.local.yaml.example .ddev/config.local.yaml

# Restart DDEV to apply the configuration and import config
ddev restart
```
After the ddev restart, configuration will be imported automatically, and you’re ready to proceed.

# Backend Task
- Create content of type “Group” (this is part of OG group content).
- This content should use a custom Pluggable Entity View Builder (PEVB) plugin to render its Full view mode.
	- The plugin behavior:
	  - If the current user is the owner of the group:
	    Display the message: “You are the group manager.”
	  - If the current user is an authenticated non-owner:
	    Display the message: “Hi user@example.com, click here if you would like to subscribe to this group called Test Group.”
	•	Notes:
	 •	user@example.com = the current display name
	 •	click = a link to the group subscribe route
	 •	Test Group = the group content title


# Testing the functionality

A unit test has been provided for this feature. To run the test, execute:
```bash
  ddev phpunit web/modules/custom/gizra_tasks/tests/src/ExistingSite/GroupSubscribeViewBuilderTest.php
```


# Frontend Task

  - Navigate to: http://drupal.ddev.site/style-guide
  - At the bottom of the style guide page, you will see two new sections:
	  1.	Person Card -> Displays a single person profile card.
    2.	Person Card (Grid) -> Displays a grid of 10 randomly generated Person Cards using dynamic sample data.
  - Design reference -> https://www.figma.com/design/r1kH05IrINkSRPTDGi665K/Home-assignment---TailwindCSS?node-id=1-1180&p=f&t=QycurGP9EK65Nuhw-0

