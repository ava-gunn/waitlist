# Update Instructions for Projects/Create.tsx

Please update the Projects/Create.tsx file with the following changes to implement template selection and add consistent padding:

## 1. Update Breadcrumbs

Simplify the breadcrumbs to only show the current page since navigation will come from the sidebar:

```tsx
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'New Project',
    href: '/projects/create',
  },
];
```

## 2. Update Imports

Add the following imports to the top of the file:

```tsx
import { type WaitlistTemplate } from '@/types/project';
import { Check } from 'lucide-react';
```

## 3. Update Component Props Interface

Add the CreateProjectProps interface after the breadcrumbs definition:

```tsx
interface CreateProjectProps {
  templates: WaitlistTemplate[];
}
```

## 4. Update Component Function Signature

Change the function signature to accept and provide default for templates:

```tsx
export default function CreateProject({ templates = [] }: CreateProjectProps) {
```

## 5. Update Form State

Add template_id to the useForm hook:

```tsx
const { data, setData, post, processing, errors } = useForm({
  name: '',
  subdomain: '',
  description: '',
  settings: {
    theme: 'light',
    collect_name: true,
    social_sharing: true,
  },
  is_active: true,
  template_id: null as number | null,
});
```

## 6. Add Padding to the Layout

Wrap the main content in a padding div for consistent spacing:

```tsx
return (
  <AppLayout breadcrumbs={breadcrumbs}>
    <Head title="Create Project" />
    <div className="p-6 md:p-8">  {/* Add this wrapper div */}
      <div className="mx-auto max-w-2xl">
        <Card>
          {/* Form content */}
        </Card>
      </div>
    </div>
  </AppLayout>
);
```

## 7. Add Template Selection UI

Add the following template selection UI before the Settings section:

```tsx
{templates.length > 0 && (
  <div className="space-y-3 pt-3">
    <h3 className="text-sm font-medium">Select a Template <span className="text-destructive">*</span></h3>
    <div className="grid gap-4 md:grid-cols-2">
      {templates.map((template) => (
        <div 
          key={template.id}
          className={`group relative overflow-hidden rounded-md border cursor-pointer transition-all hover:border-primary ${data.template_id === template.id ? 'ring-2 ring-primary' : ''}`}
          onClick={() => setData('template_id', template.id)}
        >
          <div className="absolute right-2 top-2 z-10">
            {data.template_id === template.id && (
              <div className="flex h-6 w-6 items-center justify-center rounded-full bg-primary shadow">
                <Check className="size-4 text-primary-foreground" />
              </div>
            )}
          </div>
          <div 
            className="h-32 w-full bg-cover bg-center"
            style={{
              backgroundColor: template.structure?.settings?.backgroundColor || '#f3f4f6',
              backgroundImage: template.thumbnail_path ? `url(${template.thumbnail_path})` : undefined,
            }}
            aria-hidden="true"
          />
          <div className="p-3">
            <h4 className="font-medium">{template.name}</h4>
            <p className="text-xs text-muted-foreground line-clamp-1">{template.description}</p>
          </div>
        </div>
      ))}
    </div>
    {errors.template_id && (
      <p className="text-sm text-destructive flex items-center gap-1">
        <AlertCircle className="size-3.5" />
        {errors.template_id}
      </p>
    )}
  </div>
)}
```

## 8. Add TypeScript Event Types (Optional)

To fix lint errors, update the onChange handlers with proper event types:

```tsx
onChange={(e: React.ChangeEvent<HTMLInputElement>) => setData('name', e.target.value)}
```

Apply this to all input onChange handlers in the form.
