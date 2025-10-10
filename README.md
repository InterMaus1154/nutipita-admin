# Bespoke Order Management App: Nuti Pita Admin
 
[Visit our public page](https://nutipita.co.uk/)\
[Developer and maintenar: Márk Kiss](https://markkiss.netlify.app/)

# Description 
A bespoke web application for managing orders, customers, products, invoices and finance tracking for [Nuti Pita Limited](https://nutipita.co.uk/)\

## Feature list

### Customer Management
- Customer details
- Basic CRUD operations
- Unique product pricing/customer

### Order management
- Basic CRUD operations
- Night and day time orders
- Recurring (standing) orders (per customer, per day, per product)
  - cron jobs, fully automated
- Filtering
- Sorting
- Pagination
- Dedicated page for orders that has to be done on current day
  - separated into day and night time orders
- Order summaries (total orders, total income, total for each product)
- Summary PDF for a customer of selected orders

### Product Management
- Basic CRUD operations

### Invoice Management
- Create invoices in 3 different ways:
  - for a single order
  - for a bulk of orders (within user's specified date range)
  - manually (product quantities are added manually by the user)
- Based on a PDF template
- Filtering
- Pagination

### Financial tracking
- bank statement can be uploaded as .csv
- uploaded csv can be live edited before saving (deleting columns, changing values, setting item category)
- separated income and expense filtering

### User Settings 
- toggle UI theme (black and white)
- different UI color schemes (green, pink, orange, etc...)

### UI and UX
- fully responsive
- specific support for tablet devices
- different components for mobile
      - cards on mobile, instead of table
      - pop-up card for more details
- placeholders for loading elements (clear indicator for the user that data is being loaded)
- custom select component built with Livewire and Alpine, styled as requested
