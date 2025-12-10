# FoodieHub Frontend

Modern restaurant review platform frontend built with vanilla JavaScript and Tailwind CSS.

## Structure

```
frontend/
├── public/                 # Public-facing files
│   ├── index.html         # Main landing page
│   ├── pages/             # Application pages
│   │   ├── restaurants/   # Restaurant-related pages
│   │   │   ├── restaurants.html
│   │   │   └── restaurant-detail.html
│   │   ├── profile/       # User profile pages  
│   │   │   └── profile.html
│   │   └── reviews/       # Review-related pages
│   │       └── write-review.html
│   └── assets/            # Static assets
│       ├── css/           # Stylesheets
│       ├── js/            # JavaScript modules
│       ├── images/        # Images and icons
│       └── data/          # Static data files
├── src/                   # Source files (for future build process)
└── docs/                  # Documentation
```

## Features

- **Modern UI**: Clean, responsive design with Tailwind CSS
- **Vanilla JavaScript**: No framework dependencies, pure JS
- **Modular Architecture**: Organized file structure
- **API Integration**: RESTful API communication with Laravel backend
- **Interactive Maps**: Google Maps integration for restaurant locations
- **User Authentication**: Session-based authentication
- **Image Galleries**: Restaurant photo galleries with modal viewing
- **Location Services**: Autocomplete and geolocation features

## Development

### Local Development Server

From the frontend directory:
```bash
# Python 3
python -m http.server 3000

# Node.js (if available)
npx serve -p 3000

# PHP (alternative)
php -S localhost:3000
```

Access the application at: `http://localhost:3000`

### Backend Dependency

The frontend requires the Laravel backend API to be running on `http://127.0.0.1:8000`

## API Integration

All API calls are handled through the centralized `assets/js/api.js` module:

- **Authentication**: Login, register, logout, profile management
- **Restaurants**: Browse, search, filter, details, reviews
- **Reviews**: Create, read, update, delete reviews
- **Images**: Upload and manage restaurant images
- **Statistics**: Dashboard statistics and metrics

## JavaScript Modules

- `api.js` - API client and HTTP request handling
- `main.js` - Core application logic and shared functionality
- `restaurant.js` - Restaurant listing and filtering
- `restaurant-detail.js` - Restaurant detail page and gallery
- `profile.js` - User profile management and avatar upload
- `review.js` - Review creation and management

## Styling

- **Framework**: Tailwind CSS via CDN
- **Icons**: Font Awesome 6
- **Custom CSS**: Additional styles in `assets/css/style.css`
- **Responsive**: Mobile-first responsive design

## Data Flow

1. User interacts with UI
2. JavaScript captures events
3. API calls made to Laravel backend
4. Response data rendered in UI
5. State managed in localStorage for session persistence

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

Modern ES6+ features used throughout.