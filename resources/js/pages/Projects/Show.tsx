import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { type Project, type ProjectStats, type Signup } from '@/types/project';
import { Head, Link } from '@inertiajs/react';
import { BarChart3, Download, ExternalLink, PencilLine, Plus, Users } from 'lucide-react';

interface ProjectShowProps {
  project: Project;
  stats: ProjectStats;
}

export default function ShowProject({ project, stats }: ProjectShowProps) {
  // Ensure project name is never empty in breadcrumbs
  const projectName = project.name || (project.id ? `Project ${project.id}` : "New Project");
  
  const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Projects', href: '/projects' },
    { title: projectName, href: `/projects/${project.id}` },
  ];

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title={`${projectName} - Dashboard`} />
      
      {/* Project Header */}
      <div className="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-center">
        <div>
          <div className="flex items-center gap-2">
            <h1 className="text-2xl font-bold tracking-tight">{projectName}</h1>
            <Badge variant={project.is_active ? 'default' : 'secondary'}>
              {project.is_active ? 'Active' : 'Inactive'}
            </Badge>
          </div>
          <p className="text-muted-foreground">
            <a
              href={project.full_url}
              target="_blank"
              rel="noopener noreferrer"
              className="hover:underline"
            >
              {project.full_url}
            </a>
          </p>
        </div>
        
        {/* Action Buttons */}
        <div className="flex flex-wrap gap-2">
          <Button variant="outline" size="sm" asChild>
            <Link href={project.full_url} target="_blank" rel="noopener noreferrer">
              <ExternalLink className="mr-1 size-4" />
              View Site
            </Link>
          </Button>
          <Button variant="outline" size="sm" asChild>
            <Link href={`/projects/${project.id}/edit`}>
              <PencilLine className="mr-1 size-4" />
              Edit
            </Link>
          </Button>
        </div>
      </div>

      <div className="grid gap-6 md:grid-cols-2">
        {/* Key Metrics Card */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <BarChart3 className="h-5 w-5 text-primary" />
              Key Metrics
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="grid grid-cols-2 gap-4">
              <div className="space-y-1">
                <p className="text-sm text-muted-foreground">Total Signups</p>
                <p className="text-2xl font-bold">{stats.total_signups}</p>
              </div>
              <div className="space-y-1">
                <p className="text-sm text-muted-foreground">Verified</p>
                <p className="text-2xl font-bold">{stats.verified_signups}</p>
              </div>
              <div className="space-y-1">
                <p className="text-sm text-muted-foreground">Conversion Rate</p>
                <p className="text-2xl font-bold">{stats.conversion_rate}%</p>
              </div>
              <div className="space-y-1">
                <p className="text-sm text-muted-foreground">Templates</p>
                <p className="text-2xl font-bold">{project.waitlist_templates?.length || 0}</p>
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Recent Signups Card */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Users className="h-5 w-5 text-primary" />
              Recent Signups
            </CardTitle>
          </CardHeader>
          <CardContent>
            {project.signups && project.signups.length > 0 ? (
              <div className="space-y-4">
                <ul className="divide-y">
                  {project.signups.slice(0, 5).map((signup) => (
                    <li key={signup.id} className="py-2">
                      <div className="flex items-center justify-between">
                        <div>
                          <p className="font-medium">{signup.email}</p>
                          <p className="text-sm text-muted-foreground">
                            {new Date(signup.created_at).toLocaleDateString()}
                          </p>
                        </div>
                        <Badge variant={signup.verified_at ? 'default' : 'secondary'}>
                          {signup.verified_at ? 'Verified' : 'Pending'}
                        </Badge>
                      </div>
                    </li>
                  ))}
                </ul>
                {project.signups_count && project.signups_count > 5 && (
                  <div className="text-center">
                    <Button variant="link" asChild>
                      <Link href={`/projects/${project.id}/signups`}>
                        View all {project.signups_count} signups
                      </Link>
                    </Button>
                  </div>
                )}
              </div>
            ) : (
              <div className="flex h-24 items-center justify-center">
                <p className="text-center text-muted-foreground">No signups yet</p>
              </div>
            )}
          </CardContent>
        </Card>

        {/* Templates Card */}
        <Card>
          <CardHeader>
            <CardTitle>Waitlist Templates</CardTitle>
          </CardHeader>
          <CardContent>
            {project.waitlist_templates && project.waitlist_templates.length > 0 ? (
              <ul className="divide-y">
                {project.waitlist_templates.map((template) => (
                  <li key={template.id} className="py-2">
                    <div className="flex items-center justify-between">
                      <div>
                        <p className="font-medium">{template.name}</p>
                        <p className="text-sm text-muted-foreground">
                          {template.description || 'No description'}
                        </p>
                      </div>
                      <Button variant="ghost" size="sm" asChild>
                        <Link href={`/projects/${project.id}/templates/${template.id}/edit`}>
                          <PencilLine className="size-4" />
                          <span className="sr-only">Edit</span>
                        </Link>
                      </Button>
                    </div>
                  </li>
                ))}
              </ul>
            ) : (
              <div className="flex flex-col items-center justify-center gap-2 py-6">
                <p className="text-center text-muted-foreground">No templates yet</p>
                <Button size="sm" asChild>
                  <Link href={`/projects/${project.id}/templates/create`}>
                    <Plus className="mr-1 size-4" />
                    Create Template
                  </Link>
                </Button>
              </div>
            )}
          </CardContent>
        </Card>

        {/* Export Card */}
        <Card>
          <CardHeader>
            <CardTitle>Data Export</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="flex flex-col items-center justify-center gap-4 py-4">
              <p className="text-center text-muted-foreground">
                Export your waitlist data in CSV format for analysis or import into other systems.
              </p>
              <Button variant="outline" className="w-full" asChild>
                <Link href={`/projects/${project.id}/export`}>
                  <Download className="mr-2 size-4" />
                  Export Signups
                </Link>
              </Button>
            </div>
          </CardContent>
        </Card>
      </div>
    </AppLayout>
  );
}
