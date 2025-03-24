import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { type Project, type ProjectStats, type Signup } from '@/types/project';
import { Head, Link } from '@inertiajs/react';
import { BarChart3, Download, ExternalLink, PencilLine, Plus, RefreshCw, Users } from 'lucide-react';

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
      <div className="p-4 flex flex-col justify-between gap-4 md:flex-row md:items-center">
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

      <div className="px-4 grid gap-6 md:grid-cols-2">
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
                <p className="text-sm text-muted-foreground">Template</p>
                <p className="text-2xl font-bold">{project.waitlist_template ? 1 : 0}</p>
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Recent Signups Card */}
        <Card>
          <CardHeader className="flex flex-row items-start justify-between space-y-0 pb-2">
            <CardTitle className="flex items-center gap-2">
              <Users className="h-5 w-5 text-primary" />
              Recent Signups
            </CardTitle>
            <Button variant="outline" size="sm" asChild>
              <Link href={`/projects/${project.id}/signups`}>
                View All
              </Link>
            </Button>
          </CardHeader>
          <CardContent>
            {project.signups && project.signups.length > 0 ? (
              <div className="space-y-2">
                {project.signups.map((signup: Signup) => (
                  <div key={signup.id} className="flex items-center justify-between py-2">
                    <div>
                      <div className="font-medium">{signup.name || 'Anonymous'}</div>
                      <div className="text-sm text-muted-foreground">{signup.email}</div>
                    </div>
                    <Badge variant={signup.verified_at ? 'default' : 'secondary'}>
                      {signup.verified_at ? 'Verified' : 'Pending'}
                    </Badge>
                  </div>
                ))}
              </div>
            ) : (
              <div className="py-12 text-center">
                <Users className="mx-auto h-12 w-12 text-muted-foreground/50" />
                <h3 className="mt-2 text-lg font-semibold">No signups yet</h3>
                <p className="text-muted-foreground">Share your waitlist to start collecting signups.</p>
              </div>
            )}
          </CardContent>
        </Card>
      </div>

      {/* Template Section */}
      <div className="mt-6 px-4">
        <Card>
          <CardHeader className="flex flex-row items-start justify-between space-y-0">
            <div>
              <CardTitle>Waitlist Template</CardTitle>
              <CardDescription>
                {project.waitlist_template ?
                  `Using ${project.waitlist_template.name}` :
                  'No template selected'}
              </CardDescription>
            </div>
            <Button variant="outline" size="sm" asChild>
              <Link href={`/projects/${project.id}/templates`}>
                {project.waitlist_template ? 'Change Template' : 'Select Template'}
              </Link>
            </Button>
          </CardHeader>
          <CardContent>
            {project.waitlist_template ? (
              <div className="rounded-md border p-4">
                <p className="text-sm">{project.waitlist_template.description}</p>
              </div>
            ) : (
              <div className="flex items-center justify-center rounded-md border border-dashed p-8">
                <div className="text-center">
                  <h3 className="text-lg font-medium">No Template Selected</h3>
                  <p className="mt-1 text-sm text-muted-foreground">
                    Select a template to customize your waitlist page.
                  </p>
                  <Button className="mt-4" asChild>
                    <Link href={`/projects/${project.id}/templates`}>
                      <Plus className="mr-2 h-4 w-4" />
                      Select Template
                    </Link>
                  </Button>
                </div>
              </div>
            )}
          </CardContent>
        </Card>
      </div>
    </AppLayout>
  );
}
