import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { type Project } from '@/types/project';
import { Head, Link, useForm } from '@inertiajs/react';
import { AlertCircle } from 'lucide-react';

interface EditProjectProps {
  project: Project;
}

export default function EditProject({ project }: EditProjectProps) {
  const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Projects', href: '/projects' },
    { title: project.name, href: `/projects/${project.id}` },
    { title: 'Edit', href: `/projects/${project.id}/edit` },
  ];

  const { data, setData, patch, processing, errors } = useForm({
    name: project.name,
    subdomain: project.subdomain,
    description: project.description || '',
    settings: {
      theme: project.settings?.theme || 'light',
      collect_name: project.settings?.collect_name ?? true,
      social_sharing: project.settings?.social_sharing ?? true,
    },
    is_active: project.is_active,
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    patch(`/projects/${project.id}`);
  };

  const handleSubdomainChange = (value: string) => {
    // Only allow lowercase alphanumeric characters and hyphens
    // Cannot start or end with a hyphen
    const formatted = value
      .toLowerCase()
      .replace(/[^a-z0-9-]/g, '')
      .replace(/^-+|-+$/g, '');
    
    setData('subdomain', formatted);
  };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title={`Edit ${project.name}`} />
      <div className="mx-auto max-w-2xl">
        <Card>
          <form onSubmit={handleSubmit}>
            <CardHeader>
              <CardTitle>Edit Project</CardTitle>
              <CardDescription>
                Update the details of your project.
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-6">
              <div className="space-y-4">
                <div className="space-y-2">
                  <Label htmlFor="name">
                    Project Name <span className="text-destructive">*</span>
                  </Label>
                  <Input
                    id="name"
                    value={data.name}
                    onChange={(e) => setData('name', e.target.value)}
                    placeholder="My Awesome Project"
                    aria-required="true"
                    aria-invalid={errors.name ? 'true' : 'false'}
                    aria-describedby={errors.name ? 'name-error' : undefined}
                  />
                  {errors.name && (
                    <p id="name-error" className="text-sm text-destructive flex items-center gap-1">
                      <AlertCircle className="size-3.5" />
                      {errors.name}
                    </p>
                  )}
                </div>
                
                <div className="space-y-2">
                  <Label htmlFor="subdomain">
                    Subdomain <span className="text-destructive">*</span>
                  </Label>
                  <div className="flex items-center gap-0">
                    <Input
                      id="subdomain"
                      value={data.subdomain}
                      onChange={(e) => handleSubdomainChange(e.target.value)}
                      placeholder="myproject"
                      className="rounded-r-none"
                      aria-required="true"
                      aria-invalid={errors.subdomain ? 'true' : 'false'}
                      aria-describedby={errors.subdomain ? 'subdomain-error' : 'subdomain-hint'}
                    />
                    <div className="flex h-10 items-center rounded-r-md border border-l-0 bg-muted px-3 text-sm text-muted-foreground">
                      .{window.location.host.split('.').slice(1).join('.')}
                    </div>
                  </div>
                  {errors.subdomain ? (
                    <p id="subdomain-error" className="text-sm text-destructive flex items-center gap-1">
                      <AlertCircle className="size-3.5" />
                      {errors.subdomain}
                    </p>
                  ) : (
                    <p id="subdomain-hint" className="text-sm text-muted-foreground">
                      Only lowercase letters, numbers, and hyphens. Cannot start or end with a hyphen.
                    </p>
                  )}
                </div>

                <div className="space-y-2">
                  <Label htmlFor="description">Description</Label>
                  <Input
                    id="description"
                    value={data.description}
                    onChange={(e) => setData('description', e.target.value)}
                    placeholder="A brief description of your project"
                    aria-describedby={errors.description ? 'description-error' : undefined}
                  />
                  {errors.description && (
                    <p id="description-error" className="text-sm text-destructive flex items-center gap-1">
                      <AlertCircle className="size-3.5" />
                      {errors.description}
                    </p>
                  )}
                </div>

                <div className="space-y-3 pt-3">
                  <h3 className="text-sm font-medium">Settings</h3>
                  <div className="flex items-start space-x-3">
                    <Checkbox
                      id="is_active"
                      checked={data.is_active}
                      onCheckedChange={(checked) => setData('is_active', !!checked)}
                    />
                    <div className="grid gap-1.5 leading-none">
                      <Label htmlFor="is_active" className="text-sm font-normal">
                        Active project
                      </Label>
                      <p className="text-xs text-muted-foreground">
                        When inactive, your waitlist page will not be accessible.
                      </p>
                    </div>
                  </div>
                  <div className="flex items-start space-x-3">
                    <Checkbox
                      id="collect_name"
                      checked={data.settings.collect_name}
                      onCheckedChange={(checked) => setData('settings', { ...data.settings, collect_name: !!checked })}
                    />
                    <div className="grid gap-1.5 leading-none">
                      <Label htmlFor="collect_name" className="text-sm font-normal">
                        Collect user names
                      </Label>
                      <p className="text-xs text-muted-foreground">
                        Ask users for their name when signing up to the waitlist.
                      </p>
                    </div>
                  </div>
                  <div className="flex items-start space-x-3">
                    <Checkbox
                      id="social_sharing"
                      checked={data.settings.social_sharing}
                      onCheckedChange={(checked) => setData('settings', { ...data.settings, social_sharing: !!checked })}
                    />
                    <div className="grid gap-1.5 leading-none">
                      <Label htmlFor="social_sharing" className="text-sm font-normal">
                        Enable social sharing
                      </Label>
                      <p className="text-xs text-muted-foreground">
                        Allow users to share your waitlist on social media after signing up.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </CardContent>
            <CardFooter className="flex justify-between border-t px-6 py-4">
              <Button variant="outline" type="button" asChild>
                <Link href={`/projects/${project.id}`}>Cancel</Link>
              </Button>
              <Button type="submit" disabled={processing}>
                {processing ? 'Saving...' : 'Save Changes'}
              </Button>
            </CardFooter>
          </form>
        </Card>
      </div>
    </AppLayout>
  );
}
