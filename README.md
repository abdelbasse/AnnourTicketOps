# AnnourTicketOps

## Overview

AnnourTicketOps is a ticket management application designed to streamline the handling of support tickets within an organization. It provides various functionalities for different user roles including Sub Admins, Main Admins, Supervisors, and Normal Users. The application focuses on tracking, reporting, and managing tickets efficiently, with robust logging and reporting features.

## Features

### General Functionality

- **Ticket Management:**
  - Create, update, close, and reopen tickets.
  - Transfer tickets between users.
  - Merge tickets related to the same issue.

- **Statistics & Reports:**
  - View ticket statistics and reports.
  - Analyze ticket logs for actions such as creation, updates, and closures.

### User Roles

1. **Normal User:**
   - Create and manage their own tickets.
   - View and update ticket statistics and reports.
   - Transfer tickets to other users and accept or decline transfer requests.
   - Reopen tickets within 24 hours if issues persist.

2. **Supervisor:**
   - Inherits all functionalities of a Normal User.
   - Cannot decline ticket transfers.
   - Monitor and track ticket statistics for normal users.
   - Enforce tagging of problems and add new tags.
   - View all tickets and their associated logs.

3. **Sub Admin:**
   - Manage Normal Users and Supervisors.
   - View ticket statistics and reports for all users.
   - Add and delete users and Supervisors.
   - Oversee ticket management and reporting.

4. **Main Admin:**
   - Has all functionalities of Sub Admins.
   - Can also add and manage Sub Admins.
   - Full control over ticket management and user administration.

## Application Design

### Use Cases and Diagrams

- **Use Cases:**
  - Detailed use cases for Sub Admin, Main Admin, Supervisor, and Normal User.
  - Functional requirements include ticket creation, management, and reporting.

- **Sequence Diagrams:**
  - Show interactions between users and the system for key processes.

### Database and Reporting

- **Database:**
  - Structured to track ticket details, user actions, and logs.
  - Ensure accurate and efficient data handling for reporting purposes.

- **Reporting:**
  - Export reports and ticket data to Excel.
  - Generate PDF files for tickets.

### Communication and Integration

- **External Communication:**
  - Integrates with a chat application for user communication.
  - Logs all user actions and ticket changes.

## Future Enhancements

- **File Management System:**
  - Planned integration for file management to handle attachments and related files.

- **Enhanced Reporting:**
  - Additional reporting features and statistical analysis to be added.

## Questions and Considerations

- **Ticket Merging:**
  - Tickets may be merged if related issues are identified to improve tracking.

- **Role-Based Access:**
  - Ensure appropriate permissions and access based on user roles.

## Getting Started

1. Clone the repository:
   ```bash
   git clone https://github.com/abdelbasse/AnnourTicketOps.git
