
#!/bin/bash
set -eu
perl -p -e 's/\$\{([^}]+)\}/defined $ENV{$1} ? $ENV{$1} : "\${$1}"/eg' "${1}"

