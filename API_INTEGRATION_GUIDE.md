# API Integration Guide

## Authentication
Use Laravel Sanctum tokens for API authentication. Include the bearer token in the `Authorization` header.

Example:
```bash
curl -H "Authorization: Bearer YOUR_API_TOKEN" \
     -H "Accept: application/json" \
     https://your-domain/api/v1/installations
```

## Key Endpoints
- `POST /api/v1/installations` - create installation record
- `POST /api/v1/installations/bulk` - bulk installation upload
- `GET /api/v1/installations/{id}` - installation details
- `GET /api/v1/installations` - list installations with filters
- `GET /api/v1/meters` - meter inventory
- `POST /api/v1/meters/upload` - upload meter list
- `GET /api/v1/schedules` - schedule list
- `POST /api/v1/schedules/upload` - upload schedules
- `POST /api/v1/complaints` - report complaint
- `POST /api/v1/replacements` - record meter replacements

## Integration Patterns
1. Billing sync: send completed installation data via webhook or API
2. Mobile app: authenticate and sync assigned meter lists
3. Analytics: query installations and export to BI
4. GPS integration: consume installation coordinates for mapping
5. Inventory sync: update meter status changes to external warehouse systems

## Best Practices
- Use HTTPS
- Rotate API tokens regularly
- Validate input before sending
- Handle 422 validation responses explicitly
- Respect rate limits and use pagination for large data sets
