import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Project } from '@/types/project';
import { Head, Link } from '@inertiajs/react';
import { PlusCircle } from 'lucide-react';

interface ProjectsProps {
  projects: Project[];
}

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Projects',
    href: '/projects',
  },
];

export default function Projects({ projects }: ProjectsProps) {
  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Projects" />
      <div className="mb-8 flex items-center justify-between">
        <h1 className="text-2xl font-bold tracking-tight">Your Projects</h1>
        <Button asChild>
          <Link href="/projects/create" className="flex items-center gap-1">
            <PlusCircle className="size-4" />
            <span>New Project</span>
          </Link>
        </Button>
      </div>

      {projects.length === 0 ? (
        <Card>
          <CardContent className="flex flex-col items-center justify-center p-6 text-center">
            <div className="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-primary/10">
              <PlusCircle className="size-6 text-primary" />
            </div>
            <CardTitle className="mb-2">No projects yet</CardTitle>
            <CardDescription className="mb-4">
              Create your first project to start collecting waitlist signups.
            </CardDescription>
            <Button asChild>
              <Link href="/projects/create">Create Project</Link>
            </Button>
          </CardContent>
        </Card>
      ) : (
        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          {projects.map((project) => (
            <ProjectCard key={project.id} project={project} />
          ))}
        </div>
      )}
    </AppLayout>
  );
}

interface ProjectCardProps {
  project: Project;
}

function ProjectCard({ project }: ProjectCardProps) {
  return (
    <Card className="overflow-hidden">
      <Link href={`/projects/${project.id}`}>
        <div
          className="h-32 w-full bg-cover bg-center"
          style={{
            backgroundColor: project.settings?.theme === 'dark' ? '#1f2937' : '#f3f4f6',
            backgroundImage: project.logo_path ? `url(${project.logo_path})` : undefined,
          }}
          aria-hidden="true"
        />
      </Link>
      <CardHeader className="p-4">
        <div className="flex items-center justify-between">
          <CardTitle className="line-clamp-1 text-lg">
            <Link href={`/projects/${project.id}`}>{project.name}</Link>
          </CardTitle>
          <span
            className={`inline-flex h-6 items-center rounded-full px-2 text-xs font-medium ${project.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}`}
          >
            {project.is_active ? 'Active' : 'Inactive'}
          </span>
        </div>
        <CardDescription className="line-clamp-2">
          {project.description || `${project.subdomain}.${window.location.host.split('.').slice(1).join('.')}`}
        </CardDescription>
      </CardHeader>
      <CardContent className="flex items-center justify-between border-t p-4">
        <div className="text-sm text-muted-foreground">
          {project.signups_count || 0} signups
        </div>
        <Button variant="outline" size="sm" asChild>
          <Link href={project.full_url} target="_blank" rel="noopener noreferrer">
            View Site
          </Link>
        </Button>
      </CardContent>
    </Card>
  );
}
